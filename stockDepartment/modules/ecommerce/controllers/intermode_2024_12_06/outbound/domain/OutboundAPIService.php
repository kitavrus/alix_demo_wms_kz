<?php

namespace app\modules\ecommerce\controllers\intermode\outbound\domain;

use app\modules\ecommerce\controllers\intermode\outbound\domain\constants\OutboundAPIStatus;
use app\modules\ecommerce\controllers\intermode\outbound\domain\dto\add_order\AddOrderItemRequestDTO;
use app\modules\ecommerce\controllers\intermode\outbound\domain\dto\add_order\AddOrderRequestDTO;
use app\modules\ecommerce\controllers\intermode\outbound\domain\dto\add_order\AddOrderResponseDTO;
use app\modules\ecommerce\controllers\intermode\outbound\domain\entities\EcommerceOutbound;
use app\modules\ecommerce\controllers\intermode\outbound\domain\entities\EcommerceOutboundItem;
use app\modules\ecommerce\controllers\intermode\outbound\domain\validation\ValidationOutbound;
use yii\httpclient\Client as HttpClient;
use yii\httpclient\Request;

class OutboundAPIService
{
	private $username = "WMS";
	private $password = "JA8gusyz";
	//private $baseURL = "http://10.3.172.2/trade_for_Bakhtishat/hs/ordersapi";
	private $baseURL = "http://185.233.1.45/trade_for_Bakhtishat/hs/ordersapi";
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
		$request->setUrl('orders/');
		return $request;
	}
	/**
	 * @param AddOrderResponseDTO $data
	 * @return boolean
	 */
	public function sendStatusOutOfStock($data)
	{
		return $this->sendStatus($data,OutboundAPIStatus::OUT_OF_STOCK);
	}


	/**
	 * @param AddOrderResponseDTO $data
	 * @return boolean
	 */
	public function sendStatusPickedPartWarehouse($data)
	{
		return $this->sendStatus($data,OutboundAPIStatus::PICKED_PART_WAREHOUSE);
	}

	/**
	 * @param AddOrderResponseDTO $data
	 * @return boolean
	 */
	public function sendStatusPickedWarehouse($data)
	{
		return $this->sendStatus($data,OutboundAPIStatus::PICKED_WAREHOUSE);
	}

	/**
	 * @param AddOrderResponseDTO $data
	 * @return boolean
	 */
	public function sendStatusWainingPicking($data)
	{
		return $this->sendStatus($data,OutboundAPIStatus::WAINING_PICKING);
	}

	/**
	 * @param AddOrderResponseDTO $data
	 * @return boolean
	 */
	public function sendStatusInWork($data)
	{
		return $this->sendStatus($data,OutboundAPIStatus::IN_WORK);
	}

	/**
	 * @param AddOrderResponseDTO $data
	 * @return boolean
	 */
	public function sendStatusNew($data)
	{
		return $this->sendStatus($data,OutboundAPIStatus::_NEW);
	}

	/**
	 * @param AddOrderResponseDTO $data
	 * @param string $status
	 * @return boolean
	 */
	private function sendStatus($data,$status)
	{
		$request = $this->createRequest();
		$request->setData([
			'order_id' => $data->orderNumber,
			'status' => $status,
			"items"=>$data->items,
		]);
		$response = $request->send();
		file_put_contents("sendStatus.log",date(DATE_ISO8601)."\n"."request:"."\n".print_r($request->toString(),true)."\n",FILE_APPEND);
		file_put_contents("sendStatus.log",date(DATE_ISO8601)."\n"."response:"."\n".print_r($response->toString(),true)."\n"."\n",FILE_APPEND);
		return !$response->getIsOk();
	}
}