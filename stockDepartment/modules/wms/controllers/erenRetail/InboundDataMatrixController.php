<?php

namespace app\modules\wms\controllers\erenRetail;

use common\modules\inbound\models\InboundOrder;
use Yii;
use common\modules\dataMatrix\models\InboundDataMatrix;
use common\modules\dataMatrix\models\InboundDataMatrixSerach;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\modules\client\models\Client;

/**
 * InboundDataMatrixController implements the CRUD actions for InboundDataMatrix model.
 */
class InboundDataMatrixController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all InboundDataMatrix models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new InboundDataMatrixSerach();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);


		$inboundOrders = InboundOrder::find()
									 ->select("id,order_number")
									 ->andWhere(["client_id"=>Client::CLIENT_ERENRETAIL])
			->orderBy(["created_at"=>SORT_DESC])
			->asArray()
			->all();
		$inboundOrders = ArrayHelper::map($inboundOrders,"id","order_number");
		$inboundOrdersSearch = [];
		$i = 0;
		foreach ($inboundOrders as $key=>$value) {
			if (++$i <= 150) {
				$inboundOrdersSearch [$key] = $value;
			}
		}



        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'inboundOrdersSearch' => $inboundOrdersSearch,
            'inboundOrders' => $inboundOrders,
        ]);
    }

    /**
     * Displays a single InboundDataMatrix model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new InboundDataMatrix model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new InboundDataMatrix();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing InboundDataMatrix model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing InboundDataMatrix model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the InboundDataMatrix model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return InboundDataMatrix the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = InboundDataMatrix::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
	
	
public function actionPrintDm($id)
	{
			$idm = \stockDepartment\modules\wms\models\erenRetail\InboundDataMatrix::find()
																				   ->andWhere([
																					   'id' =>$id,
																					  // 'print_status' => \stockDepartment\modules\wms\models\erenRetail\InboundDataMatrix::PRINT_STATUS_NO,
																				   ])
																				   ->one();
			$idm->print_status = \stockDepartment\modules\wms\models\erenRetail\InboundDataMatrix::PRINT_STATUS_YES;
			$idm->save(false);

			$pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8');
// set document information
			$pdf->SetCreator(PDF_CREATOR);
			$pdf->SetAuthor('wms 8d.com');
			$pdf->SetTitle('wms 8d 3PL labels');
			$pdf->SetSubject('wms 8d 3PL labels');
			$pdf->SetKeywords('wms 8d.com, receipt, box, label');

// remove default header/footer
			$pdf->setPrintHeader(false);
			$pdf->setPrintFooter(false);
// set default monospaced font
			$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
//set margins
			$pdf->SetMargins(2, 2, 2, true);
//set auto page breaks
			$pdf->SetAutoPageBreak(false, 0);
//set image scale factor
			$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
//
			$pdf->SetDisplayMode('fullpage', 'SinglePage', 'UseNone');

			$pdf->AddPage('L','NOMADEX40X60', true);
//				$pdf->AddPage('L','CUSTOM_SIZE40x60', true);

			$productStyle = $idm->product_model;
			$pdf->SetFont('dejavusans', 'B', 7);
			$pdf->MultiCell(0, 0,$productStyle , 0, 'C',false,1, 0,1);
			$style = array(
				'border' => false,
				'padding' => 0,
				'fgcolor' => array(0,0,0),
				'bgcolor' => false
			);
			$productBarcode = $idm->product_barcode;
			$dmCode = $idm->data_matrix_code;
			$pdf->write2DBarcode($dmCode, 'DATAMATRIX', 34, 5, 40, 40, $style, "N");

			$pdf->SetFont('dejavusans', 'N', 7);
			$pdf->Text(0, 5, $productBarcode);

			$dmCodeSplit = str_split($dmCode, 17);

			$pdf->Text(0, 22, $dmCodeSplit[0]);
			$pdf->Text(0, 24, $dmCodeSplit[1]);

			//$fileName = Yii::getAlias('@stockDepartment').'/web/resources/datamatrix_'.$productBarcode. '-'.$idm->id . '.pdf';
			$fileName = substr($dmCode,0,30)."-".$productBarcode. '-'.$idm->id . '.pdf';
			$pdf->Output($fileName, 'D');
			die;
	}
}

