<?php

namespace app\modules\ecommerce\controllers\intermode\inbound\domain;

use app\modules\ecommerce\controllers\intermode\inbound\domain\constants\InboundAPIStatus;
use app\modules\ecommerce\controllers\intermode\inbound\domain\dto\status_order\StatusOrderResponseDTO;
use yii\httpclient\Client as HttpClient;
use yii\httpclient\Request;

class InboundAPIService
{
	private $username = "WMS";
	private $password = "JA8gusyz";
	private $baseURL = "http://185.233.1.45/trade_for_Bakhtishat/hs/ordersapi";
//	private $baseURL = "http://10.3.172.2/trade_for_Bakhtishat/hs/ordersapi";
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
		$request->setMethod('POST');
		$request->setFormat(HttpClient::FORMAT_JSON);
		$request->setUrl('inbound');
		return $request;
	}

	/**
	 * @param StatusOrderResponseDTO $data
	 * @return boolean
	 */
	public function sendStatusInWork($data)
	{
		return $this->sendStatus($data,InboundAPIStatus::IN_WORK);
	}

	/**
	 * @param StatusOrderResponseDTO $data
	 * @return boolean
	 */
	public function sendStatusCompleted($data)
	{
		return $this->sendStatus($data,InboundAPIStatus::COMPLETED);
	}

	/**
	 * @param StatusOrderResponseDTO $data
	 * @param string $status
	 * @return boolean
	 */
	private function sendStatus($data,$status)
	{
		$request = $this->createRequest();
		$request->setData([
			'order_id' => $data->orderNumber,
			'wms_id' => $data->wmsId,
			'status' => $status,
			"items"=>$data->items,
		]);
		$response = $request->send();
		file_put_contents("sendInboundStatus.log",date(DATE_ISO8601)."\n"."Request:"."\n".print_r($request->toString(),true)."\n",FILE_APPEND);
		file_put_contents("sendInboundStatus.log",date(DATE_ISO8601)."\n"."Response:"."\n".print_r($response->toString(),true)."\n"."\n",FILE_APPEND);
		return !$response->getIsOk();
	}
}