<?php

use yii\helpers\Html;
use yii\helpers\Url;
$this->title = Yii::t('inbound/titles', 'Colins distributions file upload');
?>

<span id="buttons-menu">
    <?= Html::a(Yii::t('inbound/buttons', 'TIR upload'), '/warehouseDistribution/colins/inbound/index', ['class' => 'btn btn-primary btn-lg']) ?>
    <?= Html::a(Yii::t('inbound/buttons', 'Warehouse upload'), '/warehouseDistribution/colins/outbound/outbound-form', ['class' => 'btn btn-primary btn-lg']) ?>
</span>