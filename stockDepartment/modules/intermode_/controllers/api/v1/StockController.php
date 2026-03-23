<?php

namespace app\modules\intermode\controllers\api\v1;

use app\modules\intermode\controllers\api\v1\stock\service\StockService;
use Yii;
use yii\filters\AccessControl;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\Controller;

class StockController extends Controller
{
	public function init() {
		$this->enableCsrfValidation = false;
		$this->layout = "";
	}

    public function actionEcho($value = "")
    {
		$result = [];
		$result['echo'] = $value;
        return $this->asJson(["response"=>$result]);
    }

    public function actionRemains()
    {
		file_put_contents("StockController_actionRemains.log", 
			date(DATE_ISO8601)."\n",
			FILE_APPEND);
			
		$items = [];

		$ss = new StockService();
		$stocks = $ss->getAllStock();
		foreach ($stocks as $stock) {
			$items[] = [
				"barcode"=> $stock["product_barcode"],
				"article"=> $stock["product_model"],
				"quantity"=> $stock["product_quantity"],
				"guid"=> $stock["product_sku"],
			];
		}

        return $this->asJson([
        	"status"=>"success",
        	"message"=>"",
        	"code"=>"",
        	"items"=>$items
		]);
    }

	public function behaviors()
	{
		$behaviors = parent::behaviors();
		$behaviors['access'] = [
			'class' => AccessControl::className(),
			'rules' => [
				[
					'allow' => true,
					'roles' => ['@'],
				]
			]
		];
		$behaviors['authenticator'] = [
			'class' => HttpBearerAuth::class,
//			'optional' => ['*'],
			'only' => ['remains','echo'],

		];
		return $behaviors;
	}
}