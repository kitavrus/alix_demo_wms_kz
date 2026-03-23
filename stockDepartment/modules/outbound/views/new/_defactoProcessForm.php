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
?>
<span id="buttons-menu">
    <?= Html::tag('span', Yii::t('outbound/buttons', 'Upload outbound order DeFacto API [1]'), ['data'=>['url'=>Url::toRoute('/outbound/default/upload-file-de-facto-api')],'class' => 'btn btn-primary btn-lg', 'style' => ' margin:10px;', 'id' => 'upload-outbound-order-api-bt']) ?>
    <?= Html::tag('span', Yii::t('outbound/buttons', 'Outbound print pick list [2]'), ['data'=>['url'=>Url::toRoute('/outbound/default/select-and-print-picking-list')],'class' => 'btn btn-primary btn-lg', 'style' => ' margin:10px;', 'id' => 'outbound-print-pick-list-bt']) ?>
    <?= Html::tag('span', Yii::t('outbound/buttons', 'Begin end picking process [3]'), ['data'=>['url'=>Url::toRoute('/outbound/default/begin-end-picking-handler')],'class' => 'btn btn-primary btn-lg', 'style' => ' margin:10px;', 'id' => 'begin-end-picking-list-bt']) ?>
    <?= Html::tag('span', Yii::t('outbound/buttons', 'Scanning process [4]'), ['data'=>['url'=>Url::toRoute('/outbound/default/scanning-form')],'class' => 'btn btn-primary btn-lg', 'style' => ' margin:10px;', 'id' => 'scanning-process-bt']) ?>
    <?= Html::tag('span', Yii::t('outbound/buttons', 'Download  outbound order DeFacto API [5]'), ['data'=>['url'=>Url::toRoute('/outbound/default/download-file-de-facto-api')],'class' => 'btn btn-primary btn-lg', 'style' => ' margin:10px;', 'id' => 'download-outbound-order-api-bt']) ?>
</span>
<div id="container-outbound-layout" style="margin-top: 50px;"></div>