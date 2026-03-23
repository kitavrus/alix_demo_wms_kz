<?php

namespace app\modules\codebook\controllers;

use common\modules\outbound\models\OutboundBox;
use common\modules\outbound\service\OutboundBoxService;
use Yii;
use common\modules\codebook\models\Codebook;
use stockDepartment\modules\codebook\models\PrintAnyBarcode;
use stockDepartment\modules\codebook\models\PrintCustomerBarcode;
use stockDepartment\modules\codebook\models\CodebookSearch;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use stockDepartment\components\Controller;

/**
 * DefaultController implements the CRUD actions for Codebook model.
 */
class DefaultController extends Controller
{
//    public function behaviors()
//    {
//        $b = [
//            'verbs' => [
//                'class' => VerbFilter::className(),
//                'actions' => [
//                    'delete' => ['post'],
//                ],
//            ],
//        ];
//        return array_merge(parent::behaviors(),$b);
//    }

    /**
     * Lists all Codebook models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CodebookSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Codebook model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Codebook model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Codebook();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Codebook model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Codebook model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
//    public function actionDelete($id)
//    {
//        $this->findModel($id)->delete();
//
//        return $this->redirect(['index']);
//    }

    /**
     * Finds the Codebook model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Codebook the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Codebook::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }



    /*
     * Print any barcode
     *
     * */
    public function actionPrintBarcode()
    {
        $model = new PrintAnyBarcode();
//        OutboundBox::checkExist("123465789");
//        $lc = OutboundBoxService::getLcBarcodeFromDefacto(2);
//        VarDumper::dump($lc,10,true);
//        die;
        if($model->load(Yii::$app->request->post()) && $model->validate()) {
//            Yii::$app->response->format = 'pdf';

            return $this->render('_print-any-barcode',[
                'quantity'=>$model->quantity,
                'model'=>Codebook::findOne($model->codebook_id),

            ]);
//            $this->render('_print-any-barcode',['quantity'=>$model->quantity,'model'=>Codebook::findOne($model->codebook_id)]);
//            Yii::$app->getResponse()->redirect('')->xSendFile('1412001935-product-label.pdf');
            //return Yii::$app->response->xSendFile('1412001935-product-label.pdf');
//            $this->redirect(['print-barcode-pdf','qty'=>$model->quantity,'codebook_id'=>$model->codebook_id]);
        }

        return $this->render('print-any-barcode',['model'=>$model]);
    }

    /*
    * Print barcode
    * @param integer $codebook_id
    * @param integer $qty
    * */
    public function actionPrintBarcodePdf($codebook_id,$qty)
    {
        $LCItems = [];  // OutboundBoxService::getLcBarcodeFromDefacto($qty);
        return $this->render($this->renderCondition($codebook_id),[
            'quantity'=>$qty,
            'model'=>Codebook::findOne($codebook_id),
            'LCItems'=>$LCItems,
        ]);
    }

    private function renderCondition($codeBookId) {
        if($codeBookId == 5) {
            return '_print-outbound-defacto-barcode';
        }
        return '_print-any-barcode_orig';
    }

   /*
    * Print demo box barcode
    * */
    public function actionPrintBoxBarcodePdf()
    {
        return $this->render('_print_demo_box_label',[]);
    }

   /*
    * Print demo product barcode
    * */
    public function actionPrintProductBarcodePdf()
    {
        return $this->render('_print_demo_product_label',[]);
    }

    /*
    * Print any barcode
    *
    * */
    public function actionPrintCustomerBarcode()
    { // /codebook/default/print-customer-barcode
        $model = new PrintCustomerBarcode();
        if($model->load(Yii::$app->request->post())) {
            return $this->render('print/print-customer-barcode',['address'=>$model->address]);
        }
        return $this->render('print-customer-barcode',['model'=>$model]);
    }
}
