<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 07.10.2017
 * Time: 17:55
 */
?>
<h1 class="text-center">Defacto Ecommerce</h1>
<?//= \yii\helpers\Html::a('<h1 class="text-left" style="padding-left: 1%">'.Yii::t('buttons', 'Загрузить расходную накладную').'</h1>',['upload-order-outbound'], ['class' => 'btn btn-lg btn-default btn-block','style'=>"padding:0px"]) ?>
<?= \yii\helpers\Html::a('<h1 class="text-left" style="padding-left: 1%">'.Yii::t('buttons', 'Обработка приходов').'</h1>','/ecommerce/defacto/inbound/index', ['class' => 'btn btn-lg btn-warning btn-block text-left','style'=>"padding:0px"]) ?>
<?= \yii\helpers\Html::a('<h1 class="text-left" style="padding-left: 1%">'.Yii::t('buttons', 'ЛИСТЫ СБОРКИ').'</h1>','/ecommerce/defacto/picking/all-picking-list', ['class' => 'btn btn-lg btn-danger btn-block','style'=>"padding:0px"]) ?>
<?= \yii\helpers\Html::a('<h1 class="text-left" style="padding-left: 1%">'.Yii::t('buttons', 'Обработка расходов').'</h1>','/ecommerce/defacto/outbound/index', ['class' => 'btn btn-lg btn-success btn-block','style'=>"padding:0px"]) ?>
<?= \yii\helpers\Html::a('<h1 class="text-left" style="padding-left: 1%">'.Yii::t('buttons', 'Обработка возвратов').'</h1>','/ecommerce/defacto/return-outbound/index', ['class' => 'btn btn-lg btn-success btn-block','style'=>"padding:0px"]) ?>
<?= \yii\helpers\Html::a('<h1 class="text-left" style="padding-left: 1%">'.Yii::t('buttons', 'Частичный перерезерв').'</h1>','/ecommerce/defacto/part-re-reserved/index', ['class' => 'btn btn-lg btn-success btn-block','style'=>"padding:0px"]) ?>
<?= \yii\helpers\Html::a('<h1 class="text-left" style="padding-left: 1%">'.Yii::t('buttons', 'Отмена заказов расходов').'</h1>','/ecommerce/defacto/cancel/index', ['class' => 'btn btn-lg btn-success btn-block','style'=>"padding:0px"]) ?>

<?= '<h1 class="text-left btn btn-lg btn-default btn-block text-large" style="padding-left: 1%; padding-top:1%"></h1>'; ?>

<?= \yii\helpers\Html::a('<h1 class="text-left" style="padding-left: 1%">'.Yii::t('buttons', 'Лист отгрузки').'</h1>','/ecommerce/defacto/outbound-list/scanning-form', ['class' => 'btn btn-lg btn-default btn-block text-large','style'=>"padding:0px"]) ?>
<?= \yii\helpers\Html::a('<h1 class="text-left" style="padding-left: 1%">'.Yii::t('buttons', 'Размещение').'</h1>','/ecommerce/defacto/change-address-place/index', ['class' => 'btn btn-lg btn-default btn-block text-large','style'=>"padding:0px"]) ?>
<?= \yii\helpers\Html::a('<h1 class="text-left" style="padding-left: 1%">'.Yii::t('buttons', 'Остатки').'</h1>','/ecommerce/defacto/report/on-stock', ['class' => 'btn btn-lg btn-default btn-block text-large','style'=>"padding:0px"]) ?>
<?= \yii\helpers\Html::a('<h1 class="text-left" style="padding-left: 1%">'.Yii::t('buttons', 'Отчет приходы').'</h1>','/ecommerce/defacto/report/inbound', ['class' => 'btn btn-lg btn-default btn-block text-large','style'=>"padding:0px"]) ?>
<?= \yii\helpers\Html::a('<h1 class="text-left" style="padding-left: 1%">'.Yii::t('buttons', 'Отчет отгрузки').'</h1>','/ecommerce/defacto/report/outbound', ['class' => 'btn btn-lg btn-default btn-block text-large','style'=>"padding:0px"]) ?>
<?= \yii\helpers\Html::a('<h1 class="text-left" style="padding-left: 1%">'.Yii::t('buttons', 'Отчет отгрузки по Дефакто данным').'</h1>','/ecommerce/defacto/report/outbound-by-defacto-orders', ['class' => 'btn btn-lg btn-default btn-block text-large','style'=>"padding:0px"]) ?>
<?= \yii\helpers\Html::a('<h1 class="text-left" style="padding-left: 1%">'.Yii::t('buttons', 'Отчет по браку').'</h1>','/ecommerce/defacto/problem-report/index', ['class' => 'btn btn-lg btn-default btn-block text-large','style'=>"padding:0px"]) ?>
<?= \yii\helpers\Html::a('<h1 class="text-left" style="padding-left: 1%">'.Yii::t('buttons', 'Найти товар на складе B2C').'</h1>','/ecommerce/defacto/report/find-product-on-stock', ['class' => 'btn btn-lg btn-default btn-block text-large','style'=>"padding:0px"]) ?>

<?= '<h1 class="text-left btn btn-lg btn-default btn-block text-large" style="padding-left: 1%; padding-top:1%"></h1>'; ?>

<?= \yii\helpers\Html::a('<h1 class="text-left" style="padding-left: 1%">'.Yii::t('buttons', 'Печать этикеток').'</h1>','/ecommerce/defacto/barcode-manager/print-barcode', ['class' => 'btn btn-lg btn-default btn-block text-large','style'=>"padding:0px"]) ?>
<?= \yii\helpers\Html::a('<h1 class="text-left" style="padding-left: 1%">'.Yii::t('buttons', 'Проверка короба').'</h1>','/ecommerce/defacto/check-box/index', ['class' => 'btn btn-lg btn-default btn-block text-large','style'=>"padding:0px"]) ?>
