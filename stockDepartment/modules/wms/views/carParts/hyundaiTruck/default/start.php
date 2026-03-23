<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 07.10.2017
 * Time: 17:55
 */
?>
<h1 class="text-center">Хюндай грузовые</h1>
<?= \yii\helpers\Html::a('<h1 class="text-left" style="padding-left: 1%">'.Yii::t('buttons', '1 - Загрузить приходную накладную').'</h1>',['/wms/carParts/hyundaiTruck/default/upload-order-inbound'], ['class' => 'btn btn-lg btn-danger btn-block text-large','style'=>"padding:0px"]) ?>
<?= \yii\helpers\Html::a('<h1 class="text-left" style="padding-left: 1%">'.Yii::t('buttons', '2 - Загрузить расходную накладную').'</h1>',['/wms/carParts/hyundaiTruck/default/upload-order-outbound'], ['class' => 'btn btn-lg btn-danger btn-block','style'=>"padding:0px"]) ?>
<?= \yii\helpers\Html::a('<h1 class="text-left" style="padding-left: 1%">'.Yii::t('buttons', '3 - Обработка приходов').'</h1>',['/wms/carParts/hyundaiTruck/inbound/index'], ['class' => 'btn btn-lg btn-danger btn-block text-left','style'=>"padding:0px"]) ?>
<?php //= \yii\helpers\Html::a('<h1 class="text-left" style="padding-left: 1%">'.Yii::t('buttons', '4 - Разместить приход').'</h1>',['/wms/carParts/hyundaiTruck/place-to-address/index'], ['class' => 'btn btn-lg btn-danger btn-block','style'=>"padding:0px"]) ?>
<?= \yii\helpers\Html::a('<h1 class="text-left" style="padding-left: 1%">'.Yii::t('buttons', '4 - Обработка расходов').'</h1>',['/wms/carParts/hyundaiTruck/outbound/index'], ['class' => 'btn btn-lg btn-danger btn-block','style'=>"padding:0px"]) ?>
