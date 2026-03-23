<?php
use yii\helpers\Html;
use stockDepartment\modules\wms\assets\DeFactoAsset;
use yii\helpers\Url;

DeFactoAsset::register($this);
$this->title = Yii::t('wms/titles', 'API DeFacto');
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
<span id="buttons-menu">
    <?= Html::tag('span', Yii::t('/other/api-de-facto/buttons', 'Get INBOUND order'), [
        'class' => 'btn btn-primary btn-lg',
        'style' => ' margin:10px;',
        'id' => 'get-inbound-order-api-de-facto-bt',
        'data' => ['url'=>'/wms/defacto/api-de-facto/get-inbound-order-form'],
    ]) ?>
    <?= Html::tag('span', Yii::t('/other/api-de-facto/buttons', 'Get CROSS-DOCK order'), [
        'class' => 'btn btn-warning btn-lg',
        'style' => ' margin:10px;',
        'id' => 'get-cross-dock-order-api-de-facto-bt',
        'data' => ['url'=>'/wms/defacto/api-de-facto/get-cross-dock-order-form'],
    ]) ?>
    <?= Html::tag('span', Yii::t('/other/api-de-facto/buttons', 'Get OUTBOUND order'), [
        'class' => 'btn btn-info btn-lg',
        'style' => ' margin:10px;',
        'id' => 'get-outbound-order-api-de-facto-bt',
        'data' => ['url'=>'/wms/defacto/api-de-facto/get-outbound-order-form'],
    ]) ?>
</span>

<div id="container-api-de-facto-layout" style="margin-top: 50px;">

</div>

