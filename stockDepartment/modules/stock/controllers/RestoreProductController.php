<?php

namespace app\modules\stock\controllers;

use common\modules\stock\models\Stock;
use stockDepartment\modules\stock\service\RestoreProduct\RestoreProductSearch;
use stockDepartment\modules\stock\service\RestoreProduct\RestoreProductService;
use Yii;
use stockDepartment\components\Controller;
use yii\helpers\VarDumper;

/**
 * RestoreProductController implements the CRUD actions for Stock model.
 */
class RestoreProductController extends Controller
{
	/**
	 */
	public function actionAvailableProduct($id)
	{
		$r =  new RestoreProductService();
		$s = $r->doAvailableProduct($id);
		Yii::$app->session->setFlash('danger', "<strong>Товар:</strong>  ".
			$s->product_name ." / ".
			$s->product_brand ." / ".
			"<strong>".$s->product_barcode ."</strong> / ".
			"<strong>".$s->primary_address ."</strong> / ".
			"<strong>".$s->secondary_address ."</strong>  ".
			"<strong>"." СТАЛ ДОСТУПЕН!!!" ."</strong>  "
			);
		return $this->redirect(Yii::$app->request->getReferrer());
	}

	/**
	 */
	public function actionBlockedProduct($id)
	{
		$r =  new RestoreProductService();
		$s = $r->doBlockedProduct($id);
		Yii::$app->session->setFlash('danger', "<strong>Товар:</strong>  ".
			$s->product_name ." / ".
			$s->product_brand ." / ".
			"<strong>".$s->product_barcode ."</strong> / ".
			"<strong>".$s->primary_address ."</strong> / ".
			"<strong>".$s->secondary_address ."</strong>  ".
			"<strong>"." СТАЛ ЗАБЛОКИРОВАН!!!" ."</strong>  "
			);
		return $this->redirect(Yii::$app->request->getReferrer());
	}

   /**
	 * Lists all Stock models.
	 * @return mixed
	 */
	public function actionIndex()
	{
		$searchModel = new RestoreProductSearch();
		$dataProvider = $searchModel->searchArray(Yii::$app->request->queryParams);

		$conditionTypeArray = $searchModel->getConditionTypeArray();
		$statusArray = $searchModel->getStatusArray();
		$availabilityStatusArray = $searchModel->getAvailabilityStatusArray();
		$lostStatusArray = $searchModel->getLostStatusArray();
		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
			'conditionTypeArray' => $conditionTypeArray,
			'statusArray' => $statusArray,
			'availabilityStatusArray' => $availabilityStatusArray,
			'lostStatusArray' => $lostStatusArray,
		]);
	}
}