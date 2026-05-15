<?php

namespace stockDepartment\modules\alix\controllers\api\v1;

use stockDepartment\modules\alix\controllers\api\v1\stock\service\StockService;
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

    /**
     * GET /alix/api/v1/stock — текущие остатки склада в JSON.
     *
     * Источник: общий StockService (legacy таблица `stock`, status_availability=YES,
     * группировка по штрихкоду). Формат ответа идентичен actionRemains.
     */
    public function actionIndex()
    {
        $items = [];
        $stocks = (new StockService())->getAllStock();
        foreach ($stocks as $stock) {
            $items[] = [
                "barcode"  => $stock["product_barcode"],
                "article"  => $stock["product_model"],
                "quantity" => $stock["product_quantity"],
                "guid"     => $stock["product_sku"],
            ];
        }

        return $this->asJson([
            "status"  => "success",
            "message" => "",
            "code"    => "",
            "items"   => $items,
        ]);
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
		$behaviors['apiLogger'] = ['class' => \common\log\IncomingApiLogger::class];
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
			'only' => ['index','remains','echo'],

		];
		return $behaviors;
	}
}