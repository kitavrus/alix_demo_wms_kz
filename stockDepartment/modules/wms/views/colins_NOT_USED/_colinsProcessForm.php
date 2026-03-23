<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;
use yii\helpers\Url;
use yii\bootstrap\Modal;
//use stockDepartment\assets\OutboundAsset;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $orderNumberArray common\modules\inbound\models\InboundOrder */
/* @var $clientsArray common\modules\client\models\Client */
/* @var $outboundForm stockDepartment\modules\outbound\models\OutboundPickListForm */
//OutboundAsset::register($this);
?>
<span id="buttons-menu">
    <?= Html::tag('span', Yii::t('outbound/buttons', 'Scanning BOX'), ['data'=>['url'=>Url::toRoute('/outbound/colins/scanning-box')],'class' => 'btn btn-primary btn-lg', 'style' => ' margin:10px;', 'id' => 'scanning-box-colins-bt']) ?>
    <?= Html::tag('span', Yii::t('outbound/buttons', 'Scanning process'), ['data'=>['url'=>Url::toRoute('/outbound/colins/scanning-form')],'class' => 'btn btn-primary btn-lg', 'style' => ' margin:10px;', 'id' => 'scanning-process-colins-bt']) ?>
    <?= Html::tag('span', Yii::t('outbound/buttons', 'Inbound'), ['data'=>['url'=>Url::toRoute('/wms/colins/inbound/index')],'class' => 'btn btn-primary btn-lg', 'style' => ' margin:10px;', 'id' => 'inbound-colins-bt']) ?>
    <?= Html::tag('span', Yii::t('outbound/buttons', 'Outbound'), ['data'=>['url'=>Url::toRoute('/outbound/colins/outbound-form')],'class' => 'btn btn-primary btn-lg', 'style' => ' margin:10px;', 'id' => 'outbound-process-colins-bt']) ?>
</span>
<div id="container-outbound-layout" style="margin-top: 50px;"></div>