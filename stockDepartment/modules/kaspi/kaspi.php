<?php

namespace stockDepartment\modules\kaspi;

use stockDepartment\modules\kaspi\constants\KaspiConstants;
use stockDepartment\modules\kaspi\services\KaspiAPIService;
use stockDepartment\modules\kaspi\services\KaspiService;
use stockDepartment\modules\kaspi\services\StockService;
use yii\base\Module;

/** @property-read KaspiAPIService $apiService */
class kaspi extends Module
{
    public $controllerNamespace = 'stockDepartment\modules\kaspi\controllers';

    /** @var string|null X-Auth-Token для Kaspi API */
    public $apiToken;

    /** @var bool Включить моки ответов Kaspi */
    public $useMock = KaspiConstants::DEFAULT_USE_MOCK;

    /** @var bool Включить trace-лог HTTP запросов/ответов */
    public $httpLog = KaspiConstants::DEFAULT_HTTP_LOG;

    /** @var string|null Секрет для inbound API (X-Kaspi-Inbound-Token / Bearer) */
    public $inboundApiToken;

    /** @var bool Разрешить гостевой доступ к API (dev only) */
    public $allowGuestApi = KaspiConstants::DEFAULT_ALLOW_GUEST_API;

    /** @var string База products API */
    public $productsApiBaseUrl = KaspiConstants::PRODUCTS_API_BASE_URL;

    public function init()
    {
        parent::init();

        $apiCfg = [
            'class' => KaspiAPIService::class,
            'useMock' => (bool) $this->useMock,
            'httpLogEnabled' => (bool) $this->httpLog,
            'apiKey' => $this->apiToken,
            'productsApiBaseUrl' => (string) $this->productsApiBaseUrl,
        ];
        $this->set('apiService', $apiCfg);
        $this->set('stockService', [
            'class' => StockService::class,
        ]);
        $this->set('kaspiService', [
            'class' => KaspiService::class,
        ]);
    }
}
