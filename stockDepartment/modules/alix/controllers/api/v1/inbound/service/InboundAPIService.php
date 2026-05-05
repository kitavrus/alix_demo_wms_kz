<?php

namespace stockDepartment\modules\alix\controllers\api\v1\inbound\service;

use stockDepartment\modules\alix\controllers\api\v1\inbound\constants\InboundAPIStatus;
use stockDepartment\modules\alix\controllers\api\v1\inbound\dto\inbound_complete\InboundCompleteRequestDTO;
use stockDepartment\modules\alix\controllers\api\v1\inbound\dto\status_order\StatusOrderResponseDTO;
use yii\httpclient\Client as HttpClient;
use yii\httpclient\Request;
use yii\httpclient\Response;
use stockDepartment\modules\alix\controllers\common\apilogs\dto\AddResponse;
use stockDepartment\modules\alix\controllers\common\apilogs\ServiceApiLogs;

/**
 * Клиент 1С для уведомления о смене статуса документов приёмки/возврата.
 *
 * Единый endpoint: POST {baseURL}/ChangeDocumentStatus/
 * Payload: { order: "inbound"|"return", wms_id, status }
 *
 * Сервер/порт/имя базы и Basic-auth совпадают с сервисом получения номенклатуры
 * (см. stockDepartment\modules\kaspi\constants\KaspiConstants::ALIX_1C_*).
 */
class InboundAPIService
{
	const ORDER_INBOUND = "inbound";
	const ORDER_RETURN = "return";

	const ENDPOINT_CHANGE_STATUS = "ChangeDocumentStatus/";
	const ENDPOINT_INBOUND_COMPLETE = "InboundComplete/";

	/**
	 * Таймаут запроса к 1С, сек. Вызов синхронный (внутри UI-запроса оператора),
	 * поэтому держим его коротким — если 1С недоступна, продолжаем как при ошибке.
	 */
	const REQUEST_TIMEOUT_SECONDS = 5;

	private $username = "Kaspi";
	private $password = "525";
	private $baseURL = "http://185.249.195.105/DEV_KZ_RETAIL/hs/NMDX";

	private $httpClient;
	private $log;

	public function __construct()
	{
		$this->httpClient = new HttpClient(['baseUrl' => $this->baseURL]);
		$this->log = new ServiceApiLogs();
	}

	/**
	 * @param string $endpoint relative URL (e.g. self::ENDPOINT_CHANGE_STATUS)
	 * @return Request request instance.
	 */
	private function createRequest($endpoint = self::ENDPOINT_CHANGE_STATUS) {
		$request = $this->httpClient->createRequest();
		$request->headers->set('Authorization', 'Basic ' . base64_encode("$this->username:$this->password"));
		$request->setMethod('POST');
		$request->setFormat(HttpClient::FORMAT_JSON);
		$request->setUrl($endpoint);
		$request->setOptions([
			'timeout' => self::REQUEST_TIMEOUT_SECONDS,
		]);
		return $request;
	}

	/**
	 * @param StatusOrderResponseDTO $data
	 * @return boolean true если в 1С вернулась ошибка (сканирование продолжаем всё равно)
	 * @throws \yii\httpclient\Exception
	 */
	public function sendStatusInWorkInbound($data)
	{
		return $this->sendChangeStatus($data, self::ORDER_INBOUND, InboundAPIStatus::IN_WORK, 'inbound');
	}

	/**
	 * @param StatusOrderResponseDTO $data
	 * @return boolean
	 */
	public function sendStatusInWorkReturn($data)
	{
		return $this->sendChangeStatus($data, self::ORDER_RETURN, InboundAPIStatus::IN_WORK, 'return');
	}

	/**
	 * @param StatusOrderResponseDTO $data
	 * @param string $status InboundAPIStatus::COMPLETED | COMPLETED_WITH_DIFFERENCES
	 * @return boolean
	 */
	public function sendStatusCompletedInbound($data, $status)
	{
		return $this->sendChangeStatus($data, self::ORDER_INBOUND, $status, 'inbound');
	}

	/**
	 * @param StatusOrderResponseDTO $data
	 * @param string $status InboundAPIStatus::COMPLETED | COMPLETED_WITH_DIFFERENCES
	 * @return boolean
	 */
	public function sendStatusCompletedReturn($data, $status)
	{
		return $this->sendChangeStatus($data, self::ORDER_RETURN, $status, 'return');
	}

	/**
	 * @param StatusOrderResponseDTO $data
	 * @param string $orderType ORDER_INBOUND | ORDER_RETURN
	 * @param string $status    InboundAPIStatus::*
	 * @param string $logType   'inbound' | 'return' — выбирает метод логирования
	 * @return boolean true если ответ 1С не OK (или запрос упал)
	 */
	private function sendChangeStatus($data, $orderType, $status, $logType)
	{
		$request = $this->createRequest();
		$payload = [
			'order' => $orderType,
			'wms_id' => strval($data->wmsId),
			'status' => $status,
		];

		if ($logType === 'return') {
			$this->log->addB2BReturnRequest($data->wmsId, $data->orderNumber, $status, $payload);
		} else {
			$this->log->addB2BInboundRequest($data->wmsId, $data->orderNumber, $status, $payload);
		}

		try {
			$request->setData($payload);
			$response = $request->send();
		} catch (\Exception $e) {
			$lr = new AddResponse();
			$lr->id = $this->log->getCurrentID();
			$lr->response_data = $e->getMessage();
			$lr->response_code = 0;
			$lr->response_message = $e->getMessage();
			$this->log->addResponse($lr);
			return true;
		}

		$lr = new AddResponse();
		$lr->id = $this->log->getCurrentID();
		$lr->response_data = $response->getContent();
		$lr->response_code = $response->getStatusCode();
		$lr->response_message = $response->toString();
		$this->log->addResponse($lr);

		return !$response->getIsOk();
	}

	/**
	 * Проведение прихода в 1С после успешного завершения сканирования.
	 * Endpoint: POST {baseURL}/InboundComplete/
	 * Payload: { wms_id, status, items: [{ guid, article, barcode, quantity }] }
	 *
	 * Вызов независим от ChangeDocumentStatus — 1С использует его для фактического
	 * проведения документа поступления (списания/прихода). Ошибка не блокирует
	 * работу WMS: оператор уже завершил сканирование и должен видеть свой результат.
	 *
	 * TODO: при ошибке слать уведомление в Telegram, чтобы 1С-операторы могли
	 * вручную провести документ или дать команду на ретрай.
	 *
	 * @param InboundCompleteRequestDTO $data
	 * @return boolean true — если 1С вернула не-OK или вызов упал
	 * @throws \yii\httpclient\Exception
	 */
	public function sendInboundComplete($data)
	{
		$request = $this->createRequest(self::ENDPOINT_INBOUND_COMPLETE);

		$items = [];
		foreach ($data->items as $item) {
			$items[] = [
				'guid' => $item->guid,
				'article' => $item->article,
				'barcode' => $item->barcode,
				'quantity' => $item->quantity,
			];
		}

		$payload = [
			'wms_id' => $data->wmsId,
			'status' => $data->status,
			'items' => $items,
		];

		$this->log->addB2BInboundRequest($data->wmsId, $data->orderNumber, $data->status, $payload);

		try {
			$request->setData($payload);
			$response = $request->send();
		} catch (\Exception $e) {
			$lr = new AddResponse();
			$lr->id = $this->log->getCurrentID();
			$lr->response_data = $e->getMessage();
			$lr->response_code = 0;
			$lr->response_message = $e->getMessage();
			$this->log->addResponse($lr);
			return true;
		}

		$lr = new AddResponse();
		$lr->id = $this->log->getCurrentID();
		$lr->response_data = $response->getContent();
		$lr->response_code = $response->getStatusCode();
		$lr->response_message = $response->toString();
		$this->log->addResponse($lr);

		return !$response->getIsOk();
	}
}
