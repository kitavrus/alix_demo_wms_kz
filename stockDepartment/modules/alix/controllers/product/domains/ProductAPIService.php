<?php

namespace stockDepartment\modules\alix\controllers\product\domains;

use stockDepartment\modules\alix\controllers\ecommerce\outbound\domain\dto\add_order\AddOrderResponseDTO;
use stockDepartment\modules\alix\controllers\product\domains\dto\Filter;
use yii\httpclient\Client as HttpClient;
use yii\httpclient\Request;

class ProductAPIService
{
	 private $username = "HTTPService";
	 private $password = "httplcst134!";
	// private $baseURL = "http://185.233.1.45/KZ-trade_for_Enes/hs/NMDX/items/";
	// private $baseURL = "http://185.233.1.45/trade/hs/NMDX/items/";
	private $baseURL = "https://helpdesk.erenretail.kz/trade/hs/NMDX/items/";
	//private $baseURL = "http://185.233.1.45/trade/ws/ordersapi/orders/";
	private $httpClient;

	public function __construct()
	{
		$this->httpClient = new HttpClient(['baseUrl' => $this->baseURL]);
	}
	/**
	 * @return Request request instance.
	 */
	private function createRequest() {
		$request = $this->httpClient->createRequest();
		$request->headers->set('Authorization', 'Basic ' . base64_encode("$this->username:$this->password"));
		// $request->setMethod('POST');
		$request->setMethod('GET');
		$request->setFormat(HttpClient::FORMAT_JSON);
		return $request;
	}

	/**
	 * @param Filter|null $filter
	 * @return string raw body.
	 */
	public function getMetaData($filter = null)
	{
		$request = $this->createRequest();
		if(!empty($filter)) {
			$request->setData([
				"guid"=>$filter->guid,
				"barcode"=>$filter->barcode,
				"article"=>$filter->article
			]);
		}

		file_put_contents("getMetaData.log",date(DATE_ISO8601)."\n"."request:"."\n".print_r($request->toString(),true)."\n");

		$response = $request->send();

		file_put_contents("getMetaData.log",date(DATE_ISO8601)."\n"."response:"."\n".print_r($response->toString(),true)."\n"."\n",FILE_APPEND);
		return $response->getContent();
	}
}