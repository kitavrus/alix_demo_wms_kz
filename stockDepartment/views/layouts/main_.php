<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use stockDepartment\assets\AppAsset;
use stockDepartment\widgets\Alert;
use common\modules\billing\models\TlDeliveryProposalBilling;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<div class="wrap">
    <?php
//    if (!Yii::$app->user->isGuest) {
        NavBar::begin([
//                'brandLabel' => 'Nomadex WMS 2.0',
            'brandLabel' => 'NMDX WMS',
            'brandUrl' => Yii::$app->homeUrl,
            'options' => [
                'class' => 'navbar-inverse navbar-fixed-top',
            ],
        ]);

        $menuItems = [

            ['label' => Yii::t('titles', 'Склад для запчастей'), 'url' => '', // перево Склад
                'items' => [
                    ['label' => Yii::t('outbound/menu', 'Старт Запчасти'), 'url' => ['/wms/carParts/main/default/start']],
                    ['label' => Yii::t('outbound/menu', 'Складская тара'), 'url' => ['/placementUnit/default/index']],
                    ['label' => Yii::t('outbound/menu', 'Hyundai Auto'), 'url' => ['/wms/carParts/hyundaiAuto/default/start']],
                    ['label' => Yii::t('outbound/menu', 'Subaru'), 'url' => ['/wms/carParts/subaruAuto/default/start']],
                    ['label' => Yii::t('outbound/menu', 'Hyundai Truck'), 'url' => ['/wms/carParts/hyundaiTruck/default/start']],
                    ['label' => Yii::t('outbound/menu', 'ЛИСТЫ СБОРКИ'), 'url' => ['/wms/carParts/main/outbound/all-picking-list'],'linkOptions'=>['style'=>'background:red;']],
                    ['label' => Yii::t('outbound/menu', 'Фиксируем начало и окончание сборки'), 'url' => ['/wms/carParts/main/outbound/begin-end-picking-handler']],
                    ['label' => Yii::t('outbound/menu', 'Разместить поступления'), 'url' => ['/wms/carParts/main/place-to-address/index']],
                    ['label' => Yii::t('outbound/menu', 'Из короба в короб'), 'url' => ['/wms/carParts/main/place-to-address/box-to-box']],
                    ['label' => Yii::t('outbound/menu', 'ТТН ДЛЯ ВСЕХ'), 'url' => ['/wms/carParts/main/default/ttn-form']],
                    ['label' => Yii::t('outbound/menu', 'Отчет приходы <'), 'url' => ['/wms/carParts/main/report/inbound']],
                    ['label' => Yii::t('outbound/menu', 'Отчет отгрузки >'), 'url' => ['/wms/carParts/main/report/outbound']],
                    ['label' => Yii::t('outbound/menu', 'Отчет ТТНки'), 'url' => ['/wms/carParts/main/report/delivery-order']],
                    ['label' => Yii::t('outbound/menu', 'ABC анализ'), 'url' => ['/wms/carParts/main/abc/index']],
                    ['label' => Yii::t('outbound/menu', 'ABC анализ Зомби'), 'url' => ['/wms/carParts/main/abc/index-zombie']],
                    ['label' => Yii::t('outbound/menu', 'Администрирование'), 'url' => ['/wms/carParts/main/for-manager/entry-form']],
                    ['label' => Yii::t('outbound/menu', 'Задать размер полки'), 'url' => ['/wms/carParts/main/address-pallet-qty/index']],
					
					['label' => Yii::t('titles', 'Inventory').' Создать', 'url' => ['/wms/carParts/main/inventory/index']],
                    ['label' => Yii::t('titles', 'Inventory').' Сканирование', 'url' => ['/wms/carParts/main/inventory-process/index']],
					
                ],
            ],

            ['label' => Yii::t('titles', 'Stock'), 'url' => '', // перево Склад
                'items' => [
                    //['label' => Yii::t('titles', 'Inbound order'), 'url' => ['/inbound/default/index']],
                    //['label' => Yii::t('inbound/menu', 'Upload inbound order API'), 'url' => ['/inbound/default/upload-from-api']],
                    //['label' => Yii::t('inbound/menu', 'Download inbound order API'), 'url' => ['/inbound/default/download-from-api']],

                    //['label' => Yii::t('outbound/menu', 'Outbound order'), 'url' => ['/outbound/default/index']],
                    ['label' => Yii::t('outbound/menu', 'Add outbound order'), 'url' => ['/sheetShipment/default/index']],
//                    ['label' => Yii::t('outbound/menu', 'Add outbound order'), 'url' => ['/outbound/car-list/index']],
                   // ['label' => Yii::t('outbound/menu', 'Outbound NEW'), 'url' => ['/outbound/new/index']],

//                    ['label' => Yii::t('outbound/menu', 'Outbound print pick list'), 'url' => ['/outbound/default/print-pick-list']],
//                    ['label' => Yii::t('outbound/menu', 'Load outbound order API'), 'url' => ['/outbound/default/load-from-api']],
//                    ['label' => Yii::t('outbound/menu', 'Confirm outbound order API'), 'url' => ['/outbound/default/confirm-from-api']],
                    ['label' => Yii::t('outbound/menu', 'All pick lists'), 'url' => ['/outbound/default/picking-list-grid']],
                    ['label' => Yii::t('outbound/menu', 'Outbound box labels'), 'url' => ['/outbound/outbound-box-labels/index']],


                    ['label' => Yii::t('stock/menu', 'Accommodation'), 'url' => ['/stock/accommodation/index']],
//                    ['label' => Yii::t('titles', 'Orders'), 'url' => ['/order/order-process/steps']],
                    //['label' => Yii::t('titles', 'Products'), 'url' => ['/product/default/index']],
                    ['label' => Yii::t('titles', 'Print barcode'), 'url' => ['/codebook/default/print-barcode']],
                    ['label' => Yii::t('titles', 'Печатаем любой штрих-код'), 'url' => ['/codebook/default/print-customer-barcode']],
                    ['label' => Yii::t('titles', 'Печатаем адрес полки'), 'url' => ['/stock/address/stock-address']],
                    ['label' => Yii::t('titles', 'Lost items'), 'url' => ['/stock/lost/index']],
                    ['label' => Yii::t('titles', 'Unallocated items'), 'url' => ['/stock/accommodation/unallocated-box']],
                    ['label' => Yii::t('titles', 'Search items'), 'url' => ['/stock/stock/search-item']],
                    ['label' => Yii::t('titles', 'Inventory').' Создать', 'url' => ['/stock/inventory/index']],
                    ['label' => Yii::t('titles', 'Inventory').' Сканирование', 'url' => ['/stock/inventory-process/index']],
//                    ['label' => Yii::t('return/menu', 'Return DeFacto'), 'url' => ['/returnOrder/default-new/index']],
//                    ['label' => Yii::t('titles', 'Check returns Koton'), 'url' => ['/inbound/koton/inbound-return']],
                    //['label' => Yii::t('titles', 'API DeFacto'), 'url' => ['/other/api-de-facto/index']],
                    //['label' => Yii::t('titles', 'DeFacto CROSS-DOCK picking list'), 'url' => ['/crossDock/default/generate-cross-dock']],
                    //['label' => Yii::t('titles', 'DeFacto CROSS-DOCK confirm picking list'), 'url' => ['/crossDock/default/confirm-cross-dock']],
                   // ['label' => Yii::t('titles', 'Colins files'), 'url' => ['/inbound/colins/index']],
                   // ['label' => Yii::t('titles', 'Colins print'), 'url' => ['/inbound/colins/print-allocate-list']],
//                    ['label' => Yii::t('titles', 'Warehouse Distribution'), 'url' => ['/warehouseDistribution/default/index']],
                    ['label' => Yii::t('titles', 'Warehouse Distribution'), 'url' => ['/wms/default/index']],
					['label' => Yii::t('titles', 'Куда отгрузили'), 'url' => ['/stock/stock/where-from-box']],
                ],
            ],

//            ['label' => Yii::t('titles', 'Transport logistics'), 'url' => '',
//                'items' => [
//                    ['label' => Yii::t('titles', 'Transportation orders'), 'url' => ['/transportLogistics/tl-delivery-proposal/index']],
//                    ['label' => Yii::t('titles', 'Transportation orders cars'), 'url' => ['/transportLogistics/tl-delivery-proposal-route-cars/index']],
//                    ['label' => Yii::t('titles', 'Outbound registries'), 'url' => ['/transportLogistics/tl-outbound-registry/index']],
//                    ['label' => Yii::t('titles', 'Store Reviews'), 'url' => ['/store/store-review/index']],
//
//                ],
//            ],
            ['label' => Yii::t('titles', 'TMS'), 'url' => '',
                'items' => [
                    ['label' => Yii::t('titles', 'Transportation orders'), 'url' => ['/tms/default/index']],
                    ['label' => Yii::t('titles', 'Transportation orders cars'), 'url' => ['/tms/tl-delivery-proposal-route-cars/index']],
                    ['label' => Yii::t('titles', 'Outbound registries'), 'url' => ['/tms/tl-outbound-registry/index']],
                    ['label' => Yii::t('titles', 'Default routes'), 'url' => ['/tms/default-route/index']],
                    ['label' => Yii::t('titles', 'Route unforeseen expenses type'), 'url' => ['/tms/route-unforeseen-expenses-type/index']],
                    ['label' => Yii::t('titles', 'Agent'), 'url' => ['/tms/agent/index']],
                    ['label' => Yii::t('titles', 'Store Reviews'), 'url' => ['/store/store-review/index']],
					['label' => Yii::t('titles', 'Статус доставки'), 'url' => ['/tms/last-day-check/index']],
					['label' => Yii::t('titles', 'Старая ТТНка'), 'url' => ['/tms/default/print-old-ttn']],

                ],
            ],

/*            ['label' => Yii::t('titles', 'Leads'), 'url' => '',
                'items' => [
                    ['label' => Yii::t('titles', 'Transportation orders'), 'url' => ['/leads/lead-order/index']],
                    ['label' => Yii::t('titles', 'External clients'), 'url' => ['/leads/lead-client/index']],
                    ['label' => Yii::t('titles', 'External companies'), 'url' => ['/leads/lead-company/index']],

                ],
            ],*/

            ['label' => Yii::t('titles', 'Administration'), 'url' => '',
                'items' => [
                    ['label' => Yii::t('titles', 'Clients'), 'url' => ['/client/default/index']],
                    ['label' => Yii::t('titles', 'Employees'), 'url' => ['/employee/default/index']],
//                    ['label' => Yii::t('titles', 'Manage users'), 'url' => ['/user/admin/index']],
                    ['label' => Yii::t('titles', 'Agent'), 'url' => ['/tms/agent/index']],
//                        ['label' => Yii::t('titles', 'Car'), 'url' => ['/transportLogistics/car/index']],
//                    ['label' => Yii::t('titles', 'Warehouses'), 'url' => ['/warehouse/default/index']],
                    ['label' => Yii::t('titles', 'Stores'), 'url' => ['/store/default/index']],
                    ['label' => Yii::t('titles', 'Codebook'), 'url' => ['/codebook/default/index']],
                    ['label' => Yii::t('titles', 'Print rack address barcode'), 'url' => ['/stock/address/stock-address']],
                    ['label' => Yii::t('sheet-shipment/titles', 'ADDRESSES-FOR-OUTBOUND-PALLET'), 'url' => ['/sheetShipment/place-address/index']],
                    ['label' => Yii::t('titles', 'Country'), 'url' => ['/city/country/index']],
                    ['label' => Yii::t('titles', 'Region'), 'url' => ['/city/region/index']],
                    ['label' => Yii::t('titles', 'City'), 'url' => ['/city/default/index']],
                    ['label' => Yii::t('titles', 'Направления'), 'url' => ['/city/route-direction/index']],
                    ['label' => Yii::t('titles', 'Kpi setting'), 'url' => ['/kpiSettings/default/index']],
//                    ['label' => Yii::t('titles', 'Default routes'), 'url' => ['/transportLogistics/default-route/index']],
//                    ['label' => Yii::t('titles', 'Route unforeseen expenses type'), 'url' => ['/transportLogistics/route-unforeseen-expenses-type/index']],
                ],
            ],
            ['label' => Yii::t('titles', 'Reports'), 'url' => '/report/default/index','items'=>[
                ['label' => Yii::t('titles', 'За сегодня'), 'url' => ['/report/default/to-day']],
                ['label' => Yii::t('titles', 'Delivery proposals report'), 'url' => ['/report/default/index']],
                ['label' => Yii::t('titles', 'KPI по доставке'), 'url' => ['/report/default/chart-delivery']],
                ['label' => Yii::t('titles', 'Inbound orders report'), 'url' => ['/inbound/report/index']],
                ['label' => Yii::t('titles', 'Outbound orders report'), 'url' => ['/outbound/report/index']],
                ['label' => Yii::t('titles', 'Outbound boxes report'), 'url' => ['/outbound/box-report/index']],
                ['label' => Yii::t('titles', 'Return orders report'), 'url' => ['/returnOrder/report/index']],
                ['label' => Yii::t('titles', 'Cross-dock orders report'), 'url' => ['/crossDock/report/index']],
                ['label' => Yii::t('titles', 'Остатки на складе'), 'url' => ['/stock/stock/search-remains']],
                ['label' => Yii::t('titles', 'Учет'), 'url' => ['/bookkeeper/default/index']],
                ['label' => Yii::t('titles', 'Счета агентам'), 'url' => ['/bookkeeper/agent/index']],
                ['label' => Yii::t('titles', 'KPI сборки'), 'url' => ['/employee/kpi/picking-outbound']],
                ['label' => Yii::t('titles', 'KPI сканировки'), 'url' => ['/employee/kpi/scanning-outbound']],
                ['label' => Yii::t('titles', 'KPI приемки'), 'url' => ['/employee/kpi/inbound']],
                ['label' => Yii::t('titles', 'KPI cross-dock'), 'url' => ['/employee/kpi/cross-dock']],
//                ['label' => Yii::t('titles', 'History'), 'url' => ['/stock/stock/search-history-by-barcode']],
                ['label' => Yii::t('titles', 'Поиск возвратов'), 'url' => ['/returnOrder/tmp-order/search']],
            ]],
            ['label' => Yii::t('titles', 'Billing'), 'url' => '/billing/default/index', 'items'=>[
                ['label' => Yii::t('titles', 'Company individual'), 'url' => ['/billing/default/index', 'tariffType'=>TlDeliveryProposalBilling::TARIFF_TYPE_COMPANY_INDIVIDUAL]],
                ['label' => Yii::t('titles', 'Personal individual'), 'url' => ['/billing/default/index', 'tariffType'=>TlDeliveryProposalBilling::TARIFF_TYPE_PERSON_INDIVIDUAL]],
                ['label' => Yii::t('titles', 'Company default'), 'url' => ['/billing/default/index', 'tariffType'=>TlDeliveryProposalBilling::TARIFF_TYPE_COMPANY_DEFAULT]],
                ['label' => Yii::t('titles', 'Person default'), 'url' => ['/billing/default/index', 'tariffType'=>TlDeliveryProposalBilling::TARIFF_TYPE_PERSON_DEFAULT]],
                ['label' => Yii::t('titles', 'Agents billings'), 'url' => ['/agentBilling/default/index']],
            ]],
            ['label' => Yii::t('titles', 'DEFACTO'), 'url' => '/wms/default/route-form?id=2'],
            ['label' => Yii::t('titles', 'ASTANA'), 'url' => '/tms/astana/index',
                'items'=>[
                    ['label' => Yii::t('titles', 'Поставки'), 'url' => '/tms/astana/index'],
                    ['label' => Yii::t('titles', 'Просроки'), 'url' => '/tms/astana/last-day-delivery']
                ]
            ]
            // 6
//            , 'items'=>[
//                ['label' => Yii::t('outbound/menu', 'Заявка по маршруту'), 'url' => ['/operatorDella/route-order/index']],
//                ['label' => Yii::t('outbound/menu', 'Клиенты'), 'url' => ['/operatorDella/client/index']],
//                ['label' => Yii::t('outbound/menu', 'Заявки'), 'url' => ['/operatorDella/order/my-orders']],
//                ['label' => Yii::t('outbound/menu', 'Калькулятор'), 'url' => ['/operatorDella/tariff/calculator']],
//            ]],
        ];

/*        if(!in_array(Yii::$app->user->id,[6,30])) {
            unset($menuItems[5]['items'][0]);
        }*/

        $menuItems = \stockDepartment\components\StockRoleRule::showMainMenuByManagerType($menuItems);

        if (Yii::$app->user->isGuest) {
            $menuItems[] = ['label' => 'Login', 'url' => ['/user/security/login']];
        } else {
            $menuItems[] = [
                'label' => Yii::t('titles', 'Logout') . '(' . Yii::$app->user->identity->username . ')',
                'url' => ['/user/security/logout'],
                'linkOptions' => ['data-method' => 'post']
            ];
        }
        echo Nav::widget([
            'options' => ['class' => 'navbar-nav navbar-right'],
            'items' => $menuItems,
        ]);
        NavBar::end();
//    }
    ?>

    <div class="container-fluid">
        <br/>
        <br/>
        <br/>
        <?php if (!Yii::$app->user->isGuest) { ?>
            <?= Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]) ?>
        <?php } ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<!--<footer class="footer">-->
<!--    <div class="container">-->
<!--        <p class="pull-left">&copy; WMS 2.0 --><?//= date('Y') ?><!--</p>-->
<!---->
<!--        <p class="pull-right">Powered by --><?//= Html::a('nomadex.kz','http://nomadex.kz/') ?><!--</p>-->
<!--    </div>-->
<!--</footer>-->

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
