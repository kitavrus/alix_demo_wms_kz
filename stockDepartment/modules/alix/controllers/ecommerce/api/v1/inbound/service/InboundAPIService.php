<?php

namespace stockDepartment\modules\alix\controllers\ecommerce\api\v1\inbound\service;

use stockDepartment\modules\alix\controllers\ecommerce\api\v1\inbound\constants\InboundAPIStatus;
use stockDepartment\modules\alix\controllers\ecommerce\api\v1\inbound\dto\inbound_complete\InboundCompleteRequestDTO;
use stockDepartment\modules\alix\controllers\ecommerce\api\v1\inbound\dto\status_order\StatusOrderResponseDTO;
use yii\httpclient\Client as HttpClient;
use yii\httpclient\Request;
use yii\httpclient\Response;
use stockDepartment\modules\alix\controllers\common\apilogs\dto\AddResponse;
use stockDepartment\modules\alix\controllers\common\apilogs\ServiceApiLogs;

/**
 * Клиент 1С для уведомления о смене статуса документов приёмки.
 *
 * Единый endpoint: POST {baseURL}/ChangeDocumentStatus/
 * Payload: { order: "inbound", wms_id, status }
 */
class InboundAPIService
{
    const ORDER_INBOUND = "inbound";

    const ENDPOINT_CHANGE_STATUS = "ChangeDocumentStatus/";
    const ENDPOINT_INBOUND_COMPLETE = "InboundComplete/";

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

    private function createRequest($endpoint = self::ENDPOINT_CHANGE_STATUS)
    {
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

    public function sendStatusInWorkInbound($data)
    {
        return $this->sendChangeStatus($data, self::ORDER_INBOUND, InboundAPIStatus::IN_WORK);
    }

    public function sendStatusCompletedInbound($data, $status)
    {
        return $this->sendChangeStatus($data, self::ORDER_INBOUND, $status);
    }

    private function sendChangeStatus($data, $orderType, $status)
    {
        $request = $this->createRequest();
        $payload = [
            'order' => $orderType,
            'wms_id' => strval($data->wmsId),
            'status' => $status,
        ];

        $this->log->addB2BInboundRequest($data->wmsId, $data->orderNumber, $status, $payload);

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
     * Проведение ecommerce-приёмки в 1С после нажатия "Принять".
     * Endpoint: POST {baseURL}/InboundComplete/
     * Payload: { wms_id, status, items: [{ guid, article, barcode, quantity }] }
     *
     * Вызов независим от ChangeDocumentStatus — 1С использует его для фактического
     * проведения документа поступления. Ошибка не блокирует работу WMS:
     * оператор уже завершил приёмку и должен видеть свой результат.
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
