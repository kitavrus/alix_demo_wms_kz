<?php

namespace app\modules\wms\controllers\defacto;

use Yii;
use stockDepartment\modules\wms\models\defacto\OutboundBoxes;
use stockDepartment\modules\wms\models\defacto\OutboundBoxesSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * OutboundBoxesController implements the CRUD actions for OutboundBoxes model.
 */
class OutboundBoxesController extends  \stockDepartment\components\Controller
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
	 * Lists all OutboundBoxes models.
	 * @return mixed
	 */
	public function actionIndex()
	{
		$searchModel = new OutboundBoxesSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Finds the OutboundBoxes model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 * @param integer $id
	 * @return OutboundBoxes the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id)
	{
		if (($model = OutboundBoxes::findOne($id)) !== null) {
			return $model;
		}

		throw new NotFoundHttpException('The requested page does not exist.');
	}
}