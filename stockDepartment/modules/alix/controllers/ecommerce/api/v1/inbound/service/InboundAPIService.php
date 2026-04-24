<?php

namespace stockDepartment\modules\alix\controllers\ecommerce\api\v1\inbound\service;

use stockDepartment\modules\alix\controllers\ecommerce\api\v1\inbound\constants\InboundAPIStatus;
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

    private function createRequest()
    {
        $request = $this->httpClient->createRequest();
        $request->headers->set('Authorization', 'Basic ' . base64_encode("$this->username:$this->password"));
        $request->setMethod('POST');
        $request->setFormat(HttpClient::FORMAT_JSON);
        $request->setUrl(self::ENDPOINT_CHANGE_STATUS);
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
}
