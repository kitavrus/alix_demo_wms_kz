<?php

namespace app\modules\transportLogistics\controllers;

use common\modules\codebook\models\Codebook;
use common\modules\outbound\models\OutboundPickingLists;
use common\modules\transportLogistics\models\TlDeliveryProposal;
use Yii;
use common\modules\transportLogistics\models\TlDeliveryProposalOrderBoxes;
use common\modules\transportLogistics\models\TlDeliveryProposalOrderBoxesSearch;
use clientDepartment\components\Controller;
use yii\bootstrap\ActiveForm;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\modules\transportLogistics\usecase\orderBoxes\form\ScanningForm;
use yii\web\Response;
use clientDepartment\modules\transportLogistics\usecase\orderBoxes\Service as OrderBoxService;

/**
 * OrderBoxesController implements the CRUD actions for TlDeliveryProposalOrderBoxes model.
 */
class OrderBoxesController extends Controller
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
	 * Lists all TlDeliveryProposalOrderBoxes models.
	 * @return mixed
	 */
	public function actionScanning($delivery_proposal_id)
	{
		$form = new ScanningForm();
		$form->delivery_proposal_id = $delivery_proposal_id;
		return $this->render('scanning/scanning-form', [
			'modelForm' => $form,
			'boxes' => $this->renderPartial('scanning/_boxes', [
				'items' => ( new OrderBoxService())->getBoxes($form->delivery_proposal_id),
			]),
		]);
	}


	/*
* Scanning form handler Is Employee Barcode
* DONE
* */
	public function actionEmployeeName()
	{
		Yii::$app->response->format = Response::FORMAT_JSON;

		$errors = [];
		$messages = '';

		$model = new ScanningForm();
		$model->setScenario('IsEmployeeName');

		if (!($model->load(Yii::$app->request->post()) && $model->validate())) {
			$errors = ActiveForm::validate($model);
		}

		return [
			'success' => (empty($errors) ? 'Y' : 'N'),
			'errors' => $errors,
			'messages' => $messages,
		];
	}

	public function actionBoxBarcode()
	{
		Yii::$app->response->format = Response::FORMAT_JSON;

		$errors = [];
		$messages = '';
		$countBoxes = 0;
		$boxes = [];
		$service = new OrderBoxService();
		$model = new ScanningForm();
		$model->setScenario('IsBoxBarcode');

		if ($model->load(Yii::$app->request->post()) && $model->validate()) {
			$service->addBox($model->delivery_proposal_id,$model->box_barcode,$model->employee_name);
			$boxes = $service->getBoxes($model->delivery_proposal_id);
			$countBoxes = $service->getBoxQty($model->delivery_proposal_id);
		} else {
			$errors = \yii\widgets\ActiveForm::validate($model);
		}
		return [
			'success' => (empty($errors) ? 'Y' : 'N'),
			'errors' => $errors,
			'messages' => $messages,
			'countBoxes' => $countBoxes,
			'boxes' => $this->renderPartial('scanning/_boxes', ['items' => $boxes]),
		];
	}

	/*
	* Print box label
	* */
	public function actionPrintBoxLabel($id)
	{
		$model = $this->findModelDp($id);
		$codeBookModel = Codebook::findOne(['base_type'=>Codebook::BASE_TYPE_BOX]); // Короб

//        VarDumper::dump($codeBookModel,10,true);
//        die('----STOP----');

		return $this->render("print-box-label-pdf",[
			'model'=>$model,
			'boxes'=>( new OrderBoxService())->getBoxes($model->id),
			'codeBookModel'=>$codeBookModel
		]);
	}


	/**
	 * Finds the TlDeliveryProposal model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 * @param integer $id
	 * @return TlDeliveryProposal the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModelDp($id)
	{
		if (($model = TlDeliveryProposal::findOne($id)) !== null) {
			return $model;
		} else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}

    /**
     * Lists all TlDeliveryProposalOrderBoxes models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TlDeliveryProposalOrderBoxesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TlDeliveryProposalOrderBoxes model.
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
     * Creates a new TlDeliveryProposalOrderBoxes model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TlDeliveryProposalOrderBoxes();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TlDeliveryProposalOrderBoxes model.
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
     * Deletes an existing TlDeliveryProposalOrderBoxes model.
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
     * Finds the TlDeliveryProposalOrderBoxes model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TlDeliveryProposalOrderBoxes the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TlDeliveryProposalOrderBoxes::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
