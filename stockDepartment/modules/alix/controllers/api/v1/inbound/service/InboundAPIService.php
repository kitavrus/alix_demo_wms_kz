<?php

namespace stockDepartment\modules\alix\controllers\api\v1\inbound\service;

use stockDepartment\modules\alix\controllers\api\v1\inbound\constants\InboundAPIStatus;
use stockDepartment\modules\alix\controllers\api\v1\inbound\dto\status_order\StatusOrderResponseDTO;
use yii\httpclient\Client as HttpClient;
use yii\httpclient\Request;
use yii\httpclient\Response;
use stockDepartment\modules\alix\controllers\common\apilogs\dto\AddResponse;
use stockDepartment\modules\alix\controllers\common\apilogs\ServiceApiLogs;

class InboundAPIService
{
	private $username = "HTTPService";
	private $password = "httplcst134!";
	//private $baseURL = "http://185.233.1.45/Trade/hs/NMDX";
	private $baseURL = "https://helpdesk.erenretailTEST.kz/trade/hs/NMDX";
	//private $baseURL = "http://185.233.1.45/KZ-trade_for_Enes/hs/NMDX";
//	private $baseURL = "http://185.233.1.45/KZ-trade_for_Enes/hs/NMDX/ChangeDocumentStatus/";
	//private $baseURL = "http://10.3.172.2/KZ-trade_for_Enes/hs/NMDX/ChangeDocumentStatus/";
	private $httpClient;
	private $log;

	public function __construct()
	{
		$this->httpClient = new HttpClient(['baseUrl' => $this->baseURL]);
		$this->log = new ServiceApiLogs();
	}
	/**
	 * @return Request request instance.
	 */
	private function createRequest() {
		$request = $this->httpClient->createRequest();
		$request->headers->set('Authorization', 'Basic ' . base64_encode("$this->username:$this->password"));
		$request->setMethod('POST');
		$request->setFormat(HttpClient::FORMAT_JSON);
		//$request->setUrl('/');
		return $request;
	}

	/**
	 * @param StatusOrderResponseDTO $data
	 * @return boolean
	 * @throws \yii\httpclient\Exception
	 */
	public function sendStatusInWorkInbound($data)
	{
		$request = $this->createRequest();
		$request->setUrl("ChangeDocumentStatus/");
		$payload = [
			'order_id' => $data->orderNumber,
			'wms_id' => strval($data->wmsId),
			'status' => InboundAPIStatus::IN_WORK,
			'order' => "inbound",
		];
		
		$this->log->addB2BInboundRequest($data->wmsId,$data->orderNumber,$payload["status"],$payload);
		
		$response = $this->sendStatus($payload,$request);
		
		$lr = new AddResponse();
		$lr->id = $this->log->getCurrentID();
		$lr->response_data = $response->getContent();
		$lr->response_code = $response->getStatusCode();
		$lr->response_message = $response->toString();
		$this->log->addResponse($lr);

		return !$response->getIsOk();
	}

	/**
	 * @param StatusOrderResponseDTO $data
	 * @return boolean
	 */
	public function sendStatusInWorkReturn($data)
	{
		$request = $this->createRequest();
		$request->setUrl("ChangeDocumentStatus/");
		$payload = [
			'order_id' => $data->orderNumber,
			'wms_id' => strval($data->wmsId),
			'status' => InboundAPIStatus::IN_WORK,
			'order' => "return",
		];
		
		$this->log->addB2BReturnRequest($data->wmsId,$data->orderNumber,$payload["status"],$payload);

		$response = $this->sendStatus($payload,$request);

		$lr = new AddResponse();
		$lr->id = $this->log->getCurrentID();
		$lr->response_data = $response->getContent();
		$lr->response_code = $response->getStatusCode();
		$lr->response_message = $response->toString();
		$this->log->addResponse($lr);

		return !$response->getIsOk();
	}

	/**
	 * @param StatusOrderResponseDTO $data
	 * @return boolean
	 */
	public function sendStatusCompletedInbound($data,$status)
	{
		$request = $this->createRequest();
		$request->setUrl("InboundComplete/");
		$payload = [
			'order_id' => $data->orderNumber,
			'wms_id' => strval($data->wmsId),
			'status' => $status,
			"items"=>$data->items,
		];
		
		$this->log->addB2BInboundRequest($data->wmsId,$data->orderNumber,$payload["status"],$payload);

		$response = $this->sendStatus($payload,$request);

		$lr = new AddResponse();
		$lr->id = $this->log->getCurrentID();
		$lr->response_data = $response->getContent();
		$lr->response_code = $response->getStatusCode();
		$lr->response_message = $response->toString();
		$this->log->addResponse($lr);

		return !$response->getIsOk();
	}

	/**
	 * @param StatusOrderResponseDTO $data
	 * @return boolean
	 */
	public function sendStatusCompletedReturn($data,$status)
	{
		$request = $this->createRequest();
		$request->setUrl("ReturnOrderComplete/");
		$payload = [
			'order_id' => $data->orderNumber,
			'wms_id' => strval($data->wmsId),
			'status' => $status,
			"items"=>$data->items,
		];

		$this->log->addB2BReturnRequest($data->wmsId,$data->orderNumber,$payload["status"],$payload);

		$response = $this->sendStatus($payload,$request);

		$lr = new AddResponse();
		$lr->id = $this->log->getCurrentID();
		$lr->response_data = $response->getContent();
		$lr->response_code = $response->getStatusCode();
		$lr->response_message = $response->toString();
		$this->log->addResponse($lr);

		return !$response->getIsOk();
	}

	/**
	 * @param $payload
	 * @param $request
	 * @return boolean
	 */
	private function sendStatus($payload,$request)
	{
		$logFileNAme = "sendInboundStatus-b2b_".$payload["order_id"].".log";
		file_put_contents($logFileNAme,date(DATE_ISO8601)."\n"."Payload:"."\n".print_r($payload,true)."\n",FILE_APPEND);
		$request->setData($payload);
		file_put_contents($logFileNAme,date(DATE_ISO8601)."\n"."Request:"."\n".print_r($request->toString(),true)."\n",FILE_APPEND);
		$response = $request->send();
		file_put_contents($logFileNAme,date(DATE_ISO8601)."\n"."Response:"."\n".print_r($response->toString(),true)."\n"."\n",FILE_APPEND);
		return $response;
	}
}