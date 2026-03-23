<?php

namespace stockDepartment\modules\alix\controllers\api\v1\outbound\service;

use stockDepartment\modules\alix\controllers\api\v1\outbound\constants\OutboundAPIStatus;
use stockDepartment\modules\alix\controllers\api\v1\outbound\dto\status_order\StatusOrderLogDTO;
use stockDepartment\modules\alix\controllers\api\v1\outbound\dto\status_order\StatusOrderResponseDTO;
use yii\helpers\VarDumper;
use yii\httpclient\Client as HttpClient;
use yii\httpclient\Request;
use yii\httpclient\Response;
use stockDepartment\modules\alix\controllers\common\apilogs\dto\AddResponse;
use stockDepartment\modules\alix\controllers\common\apilogs\ServiceApiLogs;
use stockDepartment\modules\alix\controllers\common\apilogs\models\ApiLogs;

class OutboundAPIService
{
	private $username = "HTTPService";
	private $password = "httplcst134!";
	private $baseURL = "https://helpdesk.erenretailTEST.kz/trade/hs/NMDX";
//	private $baseURL = "http://185.233.1.45/Trade/hs/NMDX";
//  private $baseURL = "http://185.233.1.45/KZ-trade_for_Enes/hs/NMDX";
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
	 * @return ApiLogs
	 */
	public function sendStatusInWork($data)
	{
		$request = $this->createRequest();
		$request->setUrl("ChangeDocumentStatus/");
		$payload = [
			'order_id' => $data->orderNumber,
			'wms_id' => strval($data->wmsId),
			'status' => OutboundAPIStatus::IN_WORK,
			'order' => "outbound",
		];
		$this->log->addB2BOutboundRequest($data->wmsId,$data->orderNumber,$payload["status"],$payload);

		$response = $this->sendStatus($payload,$request);

		$lr = new AddResponse();
		$lr->id = $this->log->getCurrentID();
		$lr->response_data = $response->getContent();
		$lr->response_code = $response->getStatusCode();
		$lr->response_message = $response->toString();
		return $this->log->addResponse($lr);
	}

	/**
	 * @param StatusOrderResponseDTO $data
	 * @return ApiLogs
	 */
	public function sendStatusCompleted($data)
	{
		$request = $this->createRequest();
		$request->setUrl("OutboundComplete/");
		$payload = [
			'order_id' => $data->orderNumber,
			'wms_id' => strval($data->wmsId),
			'status' =>  OutboundAPIStatus::COMPLETED,
			"items"=>$data->items,
		];

		$this->log->addB2BOutboundRequest($data->wmsId,$data->orderNumber,$payload["status"],$payload);

		$response = $this->sendStatus($payload,$request);

		$lr = new AddResponse();
		$lr->id = $this->log->getCurrentID();
		$lr->response_data = $response->getContent();
		$lr->response_code = $response->getStatusCode();
		$lr->response_message = $response->toString();
		return $this->log->addResponse($lr);
	}

	/**
	 * @param $payload
	 * @param $request
	 * @return  Response
	 */
	private function sendStatus($payload,$request)
	{
		$logFileNAme = "sendOutboundStatus-b2b_".$payload["order_id"].".log";
		file_put_contents($logFileNAme,date(DATE_ISO8601)."\n"."Payload:"."\n".print_r($payload,true)."\n",FILE_APPEND);
		$request->setData($payload);
		file_put_contents($logFileNAme,date(DATE_ISO8601)."\n"."Request:"."\n".print_r($request->toString(),true)."\n",FILE_APPEND);
		$response = $request->send();
		file_put_contents($logFileNAme,date(DATE_ISO8601)."\n"."Response:"."\n".print_r($response->toString(),true)."\n"."\n",FILE_APPEND);
		return $response;
	}
}