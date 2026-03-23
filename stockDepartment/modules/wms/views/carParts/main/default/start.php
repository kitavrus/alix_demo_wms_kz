<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 07.10.2017
 * Time: 17:55
 */
?>
<?= \yii\helpers\Html::a('<h1>'.Yii::t('buttons', 'Hyundai Auto').'</h1>',['/wms/carParts/hyundaiAuto/default/start'], ['class' => 'btn btn-lg btn-warning btn-block text-large','style'=>"padding:20px"]) ?>
<?= \yii\helpers\Html::a('<h1>'.Yii::t('buttons', 'Subaru').'</h1>',['/wms/carParts/subaruAuto/default/start'], ['class' => 'btn btn-lg btn-success btn-block','style'=>"padding:20px"]) ?>
<?= \yii\helpers\Html::a('<h1>'.Yii::t('buttons', 'Hyundai Truck').'</h1>',['/wms/carParts/hyundaiTruck/default/start'], ['class' => 'btn btn-lg btn-danger btn-block','style'=>"padding:20px"]) ?>
<?= \yii\helpers\Html::a('<h1>'.Yii::t('buttons', 'Листы сборки').'</h1>',['/wms/carParts/main/outbound/all-picking-list'], ['class' => 'btn btn-lg btn-default btn-block text-large','style'=>"padding:20px"]) ?>
<?= \yii\helpers\Html::a('<h1>'.Yii::t('buttons', 'Фиксируем начало и окончание сборки').'</h1>',['/wms/carParts/main/outbound/begin-end-picking-handler'], ['class' => 'btn btn-lg btn-default btn-block text-large','style'=>"padding:20px"]) ?>
<?= \yii\helpers\Html::a('<h1>'.Yii::t('buttons', 'Разместить поступления').'</h1>',['/wms/carParts/main/place-to-address/index'], ['class' => 'btn btn-lg btn-default btn-block text-large','style'=>"padding:20px"]) ?>
<?= \yii\helpers\Html::a('<h1>'.Yii::t('buttons', 'ТТН ДЛЯ ВСЕХ').'</h1>',['/wms/carParts/main/default/ttn-form'], ['class' => 'btn btn-lg btn-default btn-block text-large','style'=>"padding:20px"]) ?>
<?= \yii\helpers\Html::a('<h1>'.Yii::t('buttons', 'Отчет приходы <').'</h1>',['/wms/carParts/main/report/inbound'], ['class' => 'btn btn-lg btn-default btn-block text-large','style'=>"padding:20px"]) ?>
<?= \yii\helpers\Html::a('<h1>'.Yii::t('buttons', 'Отчет отгрузки >').'</h1>',['/wms/carParts/main/report/outbound'], ['class' => 'btn btn-lg btn-default btn-block text-large','style'=>"padding:20px"]) ?>
<?= \yii\helpers\Html::a('<h1>'.Yii::t('buttons', 'Отчет ТТНки').'</h1>',['/wms/carParts/main/report/delivery-order'], ['class' => 'btn btn-lg btn-default btn-block text-large','style'=>"padding:20px"]) ?>
<?= \yii\helpers\Html::a('<h1>'.Yii::t('buttons', 'ABC анализ').'</h1>',['/wms/carParts/main/abc/index'], ['class' => 'btn btn-lg btn-default btn-block text-large','style'=>"padding:20px"]) ?>
<?= \yii\helpers\Html::a('<h1>'.Yii::t('buttons', 'Администрирование').'</h1>',['/wms/carParts/main/for-manager/entry-form'], ['class' => 'btn btn-lg btn-default btn-block text-large','style'=>"padding:20px"]) ?>
<?= \yii\helpers\Html::a('<h1>'.Yii::t('buttons', 'Задать размер полки').'</h1>',['/wms/carParts/main/address-pallet-qty/index'], ['class' => 'btn btn-lg btn-default btn-block text-large','style'=>"padding:20px"]) ?>