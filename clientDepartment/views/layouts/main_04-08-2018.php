<?php
use common\modules\client\models\ClientEmployees;
use clientDepartment\assets\AppAsset;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use common\modules\client\models\Client;
use clientDepartment\widgets\Alert;
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
    NavBar::begin([
        'brandLabel' => 'myNMDX',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    //S: TODO Сделать отдельной функцией

    $menuItems = [];

    if (Yii::$app->user->isGuest) {
        $menuItems[] = ['label' => 'Login', 'url' => ['/user/security/login']];
    } else {

        if ($client = ClientEmployees::findOne(['user_id' => Yii::$app->user->id]))
        {
            if($client->client_id == 77) // tupperware
            {
                switch ($client->manager_type) {
                    case ClientEmployees::TYPE_BASE_ACCOUNT:
//                    case ClientEmployees::TYPE_LOGIST:
//                    case ClientEmployees::TYPE_OBSERVER:
                        $menuItems = [
                            ['label' => Yii::t('titles', 'Transportation orders'), 'url' => ['/warehouseDistribution/tupperware/tl-delivery-proposal/index']],
                            ['label' => Yii::t('titles', 'Billing'), 'url' => ['/warehouseDistribution/tupperware/billing/index']],
                            ['label' => Yii::t('titles', 'Stores'), 'url' => ['/store/default/index']],
                            ['label' => Yii::t('titles', 'Reports'), 'url' => '', 'items' => [
                                ['label' => Yii::t('titles', 'KPI delivery'), 'url' => ['/warehouseDistribution/tupperware/chart-delivery/index']],
                            ]],
                            ['label' => Yii::t('titles', 'Logout') . '(' . Yii::$app->user->identity->username . ')', 'url' => ['/user/security/logout'], 'linkOptions' => ['data-method' => 'post']],
                        ];
                        break;
                    case ClientEmployees::TYPE_DIRECTOR:
//                    case ClientEmployees::TYPE_DIRECTOR_INTERN:
                        $menuItems = [
                            ['label' => Yii::t('titles', 'Transportation orders'), 'url' => ['/warehouseDistribution/tupperware/tl-delivery-proposal/index']],
                            ['label' => Yii::t('titles', 'Logout') . '(' . Yii::$app->user->identity->username . ')', 'url' => ['/user/security/logout'], 'linkOptions' => ['data-method' => 'post']],
                        ];
                        break;
                    default:
                        $menuItems = [
                            ['label' => Yii::t('titles', 'Transportation orders'), 'url' => ['/warehouseDistribution/tupperware/tl-delivery-proposal/index']],
                            ['label' => Yii::t('titles', 'Logout') . '(' . Yii::$app->user->identity->username . ')', 'url' => ['/user/security/logout'], 'linkOptions' => ['data-method' => 'post']],
                        ];
                        break;
                }


            } elseif($client->client_id == 66)
            {
                $menuItems = [
                    ['label' => Yii::t('titles', 'Transportation orders'), 'url' => ['/transportLogistics/tl-delivery-proposal/index']],
                    ['label' => Yii::t('titles', 'Outbound'), 'url' => ['/warehouseDistribution/akmaral/outbound/index']],
                    ['label' => Yii::t('titles', 'Поступления'), 'url' => ['/warehouseDistribution/akmaral/inbound/index']],
                    ['label' => Yii::t('titles', 'Reports'), 'url' => '', 'items'=>[
                        ['label' => Yii::t('titles', 'Operation report'), 'url' => ['/report/outbound/operation-report']],
                        ['label' => Yii::t('titles', 'Outbound orders report'), 'url' => ['/warehouseDistribution/akmaral/report/index']],
                        ['label' => Yii::t('titles', 'Inbound'), 'url' => ['/report/inbound/index']],
                        ['label' => Yii::t('titles', 'Остатки на складке'), 'url' => ['/report/stock/search-remains']],
                    ]],
                    ['label' => Yii::t('titles', 'Logout') . '(' . Yii::$app->user->identity->username . ')', 'url' => ['/user/security/logout'], 'linkOptions' => ['data-method' => 'post']],
                ];
            } elseif($client->id == 71) {

                $menuItems = [
                    ['label' => Yii::t('titles', 'Transportation orders'), 'url' => ['/transportLogistics/tl-delivery-proposal/index']],
                    ['label' => Yii::t('titles', 'Outbound'), 'url' => ['/warehouseDistribution/koton/outbound/index']],
                    ['label' => Yii::t('titles', 'Reports'), 'url' => '', 'items'=>[
                        ['label' => Yii::t('titles', 'Operation report'), 'url' => ['/report/outbound/operation-report']],
                        ['label' => Yii::t('titles', 'Outbound orders report'), 'url' => ['/warehouseDistribution/koton/report/index']],
//                        ['label' => Yii::t('titles', 'Outbound boxes report'), 'url' => ['/report/box-report/index']],
                        ['label' => Yii::t('titles', 'Inbound'), 'url' => ['/report/inbound/index']],
//                        ['label' => Yii::t('titles', 'Cross-dock orders report'), 'url' => ['/report/cross-dock/index']],
//                        ['label' => Yii::t('titles', 'Stock'), 'url' => ['/report/stock/index']],
                        ['label' => Yii::t('titles', 'Остатки на складке'), 'url' => ['/report/stock/search-remains']],
                    ]],
                    ['label' => Yii::t('titles', 'Return Orders'), 'url' => ['/returnOrder/default/index']],
//                    ['label' => Yii::t('titles', 'Inbound'), 'url' => ['/report/inbound/index']],
                    ['label' => Yii::t('titles', 'Logout') . '(' . Yii::$app->user->identity->username . ')', 'url' => ['/user/security/logout'], 'linkOptions' => ['data-method' => 'post']],
                ];
            } elseif(in_array($client->client_id,[97,95,96])) {

                $menuItems = [
                    ['label' => Yii::t('titles', 'Transportation orders'), 'url' => ['/transportLogistics/tl-delivery-proposal/index']],
                    ['label' => Yii::t('titles', 'Диллеры'), 'url' => ['/store/default/index']],
                    ['label' => Yii::t('titles', 'Employees'), 'url' => ['/client/employees/index']],
                    ['label' => Yii::t('titles', ''), 'url' => ['/client/employees/index']],
                    ['label' => Yii::t('titles', 'Загрузки накладных'), 'url' => '', 'items'=>[
                        ['label' => Yii::t('titles', 'Поступления'), 'url' => ['/warehouseDistribution/carParts/default/upload-order-inbound']],
                        ['label' => Yii::t('titles', 'Отгрузки'), 'url' => ['/warehouseDistribution/carParts/default/upload-order-outbound']],
                    ]],
                    ['label' => Yii::t('titles', 'Reports'), 'url' => '', 'items'=>[
                        ['label' => Yii::t('titles', 'Outbound orders report'), 'url' => ['/report/outbound/index']],
                        ['label' => Yii::t('titles', 'Inbound'), 'url' => ['/report/inbound/index']],
                        ['label' => Yii::t('titles', 'Stock'), 'url' => ['/report/stock/index']],
                        ['label' => Yii::t('titles', 'Остатки'), 'url' => ['/report/stock/search-remains']],
                    ]],
                    ['label' => Yii::t('titles', 'Logout') . '(' . Yii::$app->user->identity->username . ')', 'url' => ['/user/security/logout'], 'linkOptions' => ['data-method' => 'post']],
                ];

            } else {
                switch ($client->manager_type) {

                    case ClientEmployees::TYPE_BASE_ACCOUNT:
                    case ClientEmployees::TYPE_LOGIST:
                    case ClientEmployees::TYPE_OBSERVER:
                        $menuItems = [
                            ['label' => Yii::t('titles', 'Transportation orders'), 'url' => ['/transportLogistics/tl-delivery-proposal/index']],
                            ['label' => Yii::t('titles', 'Stores'), 'url' => ['/store/default/index']],
                            ['label' => Yii::t('titles', 'Employees'), 'url' => ['/client/employees/index']],
                            ['label' => Yii::t('titles', 'Billing'), 'url' => ['/report/billing/index']],
                            ['label' => Yii::t('titles', 'Reports'), 'url' => '', 'items'=>[
                                ['label' => Yii::t('titles', 'Operation report'), 'url' => ['/report/outbound/operation-report']],
                                ['label' => Yii::t('titles', 'Outbound orders report'), 'url' => ['/report/outbound/index']],
                                ['label' => Yii::t('titles', 'Outbound boxes report'), 'url' => ['/report/box-report/index']],
                                ['label' => Yii::t('titles', 'Inbound'), 'url' => ['/report/inbound/index']],
                                ['label' => Yii::t('titles', 'Cross-dock orders report'), 'url' => ['/report/cross-dock/index']],
                                ['label' => Yii::t('titles', 'Stock'), 'url' => ['/report/stock/index']],
                                ['label' => Yii::t('titles', 'Remains of the stock'), 'url' => ['/report/stock/search-remains']],
                                ['label' => Yii::t('titles', 'History by barcode'), 'url' => ['/report/stock/search-history-by-barcode']],
                                ['label' => Yii::t('titles', 'KPI delivery'), 'url' => ['/report/chart-delivery/index']],
                                ['label' => Yii::t('titles', 'Lost lot'), 'url' => ['/report/lost/index']],
                            ]],

                        ];

                        if($client->client_id == Client::CLIENT_COLINS) {
                            $menuItems[4]['items'][5] =  ['label' => Yii::t('titles', 'Остатки'), 'url' => ['/report/stock/search-remains']];
                        }


                        if($client->client_id == Client::CLIENT_DEFACTO){
                            $menuItems[4]['items'][] =  ['label' => Yii::t('titles', 'Return orders report'), 'url' => ['/report/return/index']];
                            $menuItems[4]['items'][] =  ['label' => Yii::t('titles', 'Inventory'), 'url' => ['/report/stock/inventory']];
                            $menuItems[4]['items'][] =  ['label' => Yii::t('titles', 'Export Label'), 'url' => ['/report/outbound/export-label']];
                        }

                        if($client->client_id == Client::CLIENT_COLINS){
//                            $menuItems[ ] = ['label' => Yii::t('titles', 'Reports'), 'url' => '', 'items'=>[
                            $menuItems[ ] =
                             ['label' => Yii::t('titles', 'Upload files'), 'url' => ['/warehouseDistribution/colins/default/file-upload']];
                        }

                        $menuItems[ ] =  ['label' => Yii::t('titles', 'Profile settings'), 'url' => ['/client/default/profile']];
                        $menuItems[ ]  = ['label' => Yii::t('titles', 'Logout') . '(' . Yii::$app->user->identity->username . ')', 'url' => ['/user/security/logout'], 'linkOptions' => ['data-method' => 'post']];
                        break;

                    case ClientEmployees::TYPE_OBSERVER_NO_TARIFF:
                        $menuItems = [
                            ['label' => Yii::t('titles', 'Transportation orders'), 'url' => ['/transportLogistics/tl-delivery-proposal/index']],
                            ['label' => Yii::t('titles', 'Stores'), 'url' => ['/store/default/index']],
                            ['label' => Yii::t('titles', 'Employees'), 'url' => ['/client/employees/index']],
//                        ['label' => Yii::t('titles', 'Reports'), 'url' => ['/report/default/index']],
//                                        ['label' => Yii::t('titles', 'Billing'), 'url' => ['/report/billing/index']],
                            ['label' => Yii::t('titles', 'Profile settings'), 'url' => ['/client/default/profile']],
                            ['label' => Yii::t('titles', 'Logout') . '(' . Yii::$app->user->identity->username . ')', 'url' => ['/user/security/logout'], 'linkOptions' => ['data-method' => 'post']],
                        ];
                        break;

                    case ClientEmployees::TYPE_DIRECTOR:
                    case ClientEmployees::TYPE_DIRECTOR_INTERN:
                        $menuItems = [
                            ['label' => Yii::t('titles', 'Transportation orders'), 'url' => ['/transportLogistics/tl-delivery-proposal/index']],
                            ['label' => Yii::t('titles', 'Store'), 'url' => ['/store/default/view-redirect']],
                            ['label' => Yii::t('titles', 'Employees'), 'url' => ['/client/employees/index']],
                            ['label' => Yii::t('titles', 'Profile settings'), 'url' => ['/client/default/profile']],
                            ['label' => Yii::t('titles', 'Logout') . '(' . Yii::$app->user->identity->username . ')', 'url' => ['/user/security/logout'], 'linkOptions' => ['data-method' => 'post']],
                        ];
                        break;

                    case ClientEmployees::TYPE_MANAGER:
                    case ClientEmployees::TYPE_MANAGER_INTERN:
                        $menuItems = [
                            ['label' => Yii::t('titles', 'Transportation orders'), 'url' => ['/transportLogistics/tl-delivery-proposal/index']],
//                        ['label' => Yii::t('titles', 'Магазин'), 'url' => ['/store/default/view-redirect']],
                            ['label' => Yii::t('titles', 'Profile settings'), 'url' => ['/client/default/profile']],
                            ['label' => Yii::t('titles', 'Logout') . '(' . Yii::$app->user->identity->username . ')', 'url' => ['/user/security/logout'], 'linkOptions' => ['data-method' => 'post']],
                        ];

                        break;

//                    case ClientEmployees::TYPE_REGIONAL_OBSERVER:
                    case ClientEmployees::TYPE_REGIONAL_OBSERVER_RUSSIA:
                    case ClientEmployees::TYPE_REGIONAL_OBSERVER_BELARUS:
                        $menuItems = [
                            ['label' => Yii::t('titles', 'Transportation orders'), 'url' => ['/transportLogistics/tl-delivery-proposal/index']],
                            //['label' => Yii::t('titles', 'Stores'), 'url' => ['/store/default/index']],
//                            ['label' => Yii::t('titles', 'Employees'), 'url' => ['/client/employees/index']],
//                        ['label' => Yii::t('titles', 'Reports'), 'url' => ['/report/default/index']],
//                                        ['label' => Yii::t('titles', 'Billing'), 'url' => ['/report/billing/index']],
                            ['label' => Yii::t('titles', 'Profile settings'), 'url' => ['/client/default/profile']],
                            ['label' => Yii::t('titles', 'Logout') . '(' . Yii::$app->user->identity->username . ')', 'url' => ['/user/security/logout'], 'linkOptions' => ['data-method' => 'post']],
                        ];
                        break;

                    default:
                        break;
                }
            }

        }
    }

    //D: TODO Сделать отдельной функцией

    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $menuItems,
    ]);
    NavBar::end();
    ?>

    <div class="container-fluid">
        <br/>
        <br/>
        <br/>
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<!--<footer class="footer">-->
<!--    <div class="container">-->
<!--        <p class="pull-left">&copy; My Company --><?//= date('Y') ?><!--</p>-->
<!---->
<!--        <p class="pull-right">Powered by --><?//= Html::a('nomadex.kz', 'http://nomadex.kz/') ?><!--</p>-->
<!--    </div>-->
<!--</footer>-->

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
