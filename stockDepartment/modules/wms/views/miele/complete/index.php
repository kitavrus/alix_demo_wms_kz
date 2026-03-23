<?php
use stockDepartment\modules\wms\assets\DeFactoAsset;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
?>

<?= Html::a("Поступления", ['show-inbound'], [
    'class' => 'btn btn-primary btn-lg btn-href',
    'style' => ' margin:10px;',
]) ?>

<?= Html::a("Отгрузки", ['show-outbound'], [
    'class' => 'btn btn-danger btn-lg btn-href',
    'style' => ' margin:10px;',
]) ?>

<?= Html::a("Перемещения", ['show-movement'], [
    'class' => 'btn btn-warning btn-lg btn-href',
    'style' => ' margin:10px;',
]) ?>