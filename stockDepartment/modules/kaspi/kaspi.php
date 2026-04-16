<?php

namespace stockDepartment\modules\kaspi;

use stockDepartment\modules\kaspi\constants\KaspiConstants;
use stockDepartment\modules\kaspi\services\Alix1CApiService;
use stockDepartment\modules\kaspi\services\KaspiAPIService;
use stockDepartment\modules\kaspi\services\KaspiService;
use stockDepartment\modules\kaspi\services\OrderReturnService;
use stockDepartment\modules\kaspi\services\PriceListService;
use stockDepartment\modules\kaspi\services\PriceService;
use stockDepartment\modules\kaspi\services\ProductSyncService;
use stockDepartment\modules\kaspi\services\StockHistoryService;
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

    /** @var string База сервиса Alix 1C (GET /items, мастер-данные номенклатуры) */
    public $alix1cBaseUrl = KaspiConstants::ALIX_1C_BASE_URL;

    /** @var string Basic Auth — логин Alix 1C */
    public $alix1cUsername = KaspiConstants::ALIX_1C_DEFAULT_USERNAME;

    /** @var string Basic Auth — пароль Alix 1C */
    public $alix1cPassword = KaspiConstants::ALIX_1C_DEFAULT_PASSWORD;

    /** @var int Таймаут Alix 1C запросов, сек */
    public $alix1cTimeoutSeconds = KaspiConstants::ALIX_1C_DEFAULT_TIMEOUT;

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
        $this->set('priceListService', [
            'class' => PriceListService::class,
        ]);
        $this->set('priceService', [
            'class' => PriceService::class,
        ]);
        $this->set('stockHistoryService', [
            'class' => StockHistoryService::class,
        ]);
        $this->set('orderReturnService', [
            'class' => OrderReturnService::class,
        ]);
        $this->set('alix1cApiService', [
            'class' => Alix1CApiService::class,
            'baseUrl' => (string) $this->alix1cBaseUrl,
            'username' => (string) $this->alix1cUsername,
            'password' => (string) $this->alix1cPassword,
            'timeoutSeconds' => (int) $this->alix1cTimeoutSeconds,
            'httpLogEnabled' => (bool) $this->httpLog,
        ]);
        $this->set('productSyncService', [
            'class' => ProductSyncService::class,
        ]);
    }
}
