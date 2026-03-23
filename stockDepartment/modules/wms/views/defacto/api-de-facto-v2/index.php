<?php
use yii\helpers\Html;
use stockDepartment\modules\wms\assets\DeFactoAsset;
use yii\helpers\Url;

DeFactoAsset::register($this);
$this->title = Yii::t('wms/titles', 'API DeFacto v2');
?>

<?= Html::label(Yii::t('inbound/forms', 'Client ID')); ?>
<?= Html::dropDownList( 'client_id',\common\modules\client\models\Client::CLIENT_DEFACTO,$clientsArray, [
        'prompt' => '',
        'id' => 'main-form-client-id',
        'class' => 'form-control input-lg',
        'data'=>['url'=>Url::to('/wms/default/route-form')],
        'readonly' => true,
        'name' => 'InboundForm[client_id]',
    ]
); ?>
<h1><?=$this->title?></h1>
<div id="buttons-menu">
    <?= Html::tag('span', Yii::t('/other/api-de-facto/buttons', 'Получаем список ПРИХОДОВ').' '.'<span class="loading"></span>', [
        'class' => 'btn btn-primary btn-lg',
        'style' => ' margin:5px;',
        'id' => 'get-inbound-order-api-de-facto-bt',
        'data' => ['url'=>Url::to(['get-inbound-order-grid'])],
    ]) ?>

    <?= Html::tag('span', Yii::t('/other/api-de-facto/buttons', 'Получаем список РАСХОДОВ').' '.'<span class="loading"></span>', [
        'class' => 'btn btn-warning btn-lg',
        'style' => ' margin:5px;',
        'id' => 'get-outbound-order-api-de-facto-bt',
        'data' => ['url'=>Url::to(['get-outbound-order-grid'])],
    ]) ?>
    <?= Html::tag('span', Yii::t('/other/api-de-facto/buttons', 'Получаем список ВОЗВРАТОВ').' '.'<span class="loading"></span>', [
        'class' => 'btn btn-info btn-lg',
        'style' => ' margin:5px;',
        'id' => 'get-returns-order-api-de-facto-bt',
        'data' => ['url'=>Url::to(['get-return-order-grid'])],
    ]) ?>
    <?= Html::a(Yii::t('/other/api-de-facto/buttons', 'ЗАРЕЗЕРВИРОВАТЬ СБОРКИ'),'/other/one/allocate-test', [
        'class' => 'btn btn-danger btn-lg-',
        'style' => ' margin:5px;',
        ]
    ) ?>
</div>
<div id="container-api-de-facto-layout" style="margin-top: 50px;">
</div>