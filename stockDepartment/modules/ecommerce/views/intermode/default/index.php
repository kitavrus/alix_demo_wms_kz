<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 24.09.2024
 * Time: 17:55
 */
use yii\helpers\Html;
?>
<h1 class="text-center">Ecommerce</h1>
<?= \yii\helpers\Html::a('<h1 class="text-left" style="padding-left: 1%">'.Yii::t('buttons', 'Обработка приходов').'</h1>','/ecommerce/intermode/inbound/scanning/scanning-form', ['class' => 'btn btn-lg btn-warning btn-block text-left','style'=>"padding:0px"]) ?>
<?= \yii\helpers\Html::a('<h1 class="text-left" style="padding-left: 1%">'.Yii::t('buttons', 'ЛИСТЫ СБОРКИ').'</h1>','/ecommerce/intermode/outbound/picking/lists', ['class' => 'btn btn-lg btn-danger btn-block','style'=>"padding:0px"]) ?>
<?= \yii\helpers\Html::a('<h1 class="text-left" style="padding-left: 1%">'.Yii::t('buttons', 'Обработка расходов').'</h1>','/ecommerce/intermode/outbound/scanning/index', ['class' => 'btn btn-lg btn-success btn-block','style'=>"padding:0px"]) ?>
<?//= \yii\helpers\Html::a('<h1 class="text-left" style="padding-left: 1%">'.Yii::t('buttons', 'Обработка возвратов').'</h1>','/ecommerce/defacto/return-outbound/index', ['class' => 'btn btn-lg btn-success btn-block','style'=>"padding:0px"]) ?>
<?//= \yii\helpers\Html::a('<h1 class="text-left" style="padding-left: 1%">'.Yii::t('buttons', 'Частичный перерезерв').'</h1>','/ecommerce/defacto/part-re-reserved/index', ['class' => 'btn btn-lg btn-success btn-block','style'=>"padding:0px"]) ?>

<?//= \yii\helpers\Html::a('<h1 class="text-left" style="padding-left: 1%">'.Yii::t('buttons', 'Отменить заказ если его отменил клиент').'</h1>','/ecommerce/defacto/cancel-by-client', ['class' => 'btn btn-lg btn-success btn-block','style'=>"padding:0px"]) ?>
<?//= \yii\helpers\Html::a('<h1 class="text-left" style="padding-left: 1%">'.Yii::t('buttons', 'Отмена заказов расходов').'</h1>','/ecommerce/defacto/cancel/index', ['class' => 'btn btn-lg btn-success btn-block','style'=>"padding:0px"]) ?>
<?//= \yii\helpers\Html::a('<h1 class="text-left" style="padding-left: 1%">'.Yii::t('buttons', 'Изменить остатки на складе').'</h1>','/ecommerce/defacto/stock-adjustment/index', ['class' => 'btn btn-lg btn-success btn-block','style'=>"padding:0px"]) ?>
<!---->
<?//= \yii\helpers\Html::a('<h1 class="text-left" style="padding-left: 1%">'.Yii::t('buttons', 'ТРАНСФЕРА (ЛИСТЫ СБОРКИ) ').'</h1>','/ecommerce/defacto/transfer/all-pick-list', ['class' => 'btn btn-lg btn-success btn-block','style'=>"padding:0px"]) ?>
<?//= \yii\helpers\Html::a('<h1 class="text-left" style="padding-left: 1%">'.Yii::t('buttons', 'Обработка трансферов').'</h1>','/ecommerce/defacto/transfer', ['class' => 'btn btn-lg btn-success btn-block','style'=>"padding:0px"]) ?>
<?//= \yii\helpers\Html::a('<h1 class="text-left" style="padding-left: 1%">'.Yii::t('buttons', 'Загрузка приходных накладных').'</h1>','/ecommerce/defacto/inbound-api/index', ['class' => 'btn btn-lg btn-success btn-block','style'=>"padding:0px"]) ?>

<?//= '<h1 class="text-left btn btn-lg btn-default btn-block text-large" style="padding-left: 1%; padding-top:1%"></h1>'; ?>

<?//= \yii\helpers\Html::a('<h1 class="text-left" style="padding-left: 1%">'.Yii::t('buttons', 'Лист отгрузки').'</h1>','/ecommerce/defacto/outbound-list/scanning-form', ['class' => 'btn btn-lg btn-default btn-block text-large','style'=>"padding:0px"]) ?>
<?//= \yii\helpers\Html::a('<h1 class="text-left" style="padding-left: 1%">'.Yii::t('buttons', 'Размещение').'</h1>','/ecommerce/defacto/change-address-place/index', ['class' => 'btn btn-lg btn-default btn-block text-large','style'=>"padding:0px"]) ?>
<?//= \yii\helpers\Html::a('<h1 class="text-left" style="padding-left: 1%">'.Yii::t('buttons', 'Создать пустой приход').'</h1>','/ecommerce/defacto/default/create', ['class' => 'btn btn-lg btn-default btn-block text-large','style'=>"padding:0px"]) ?>
<?//= Html::a('<h1 class="text-left" style="padding-left: 1%">'.Yii::t('buttons', 'Создать листы для проверки').'</h1>','/ecommerce/defacto/check-on-stock/index', ['class' => 'btn btn-lg btn-default btn-block text-large','style'=>"padding:0px"]) ?>
<!---->
<?= \yii\helpers\Html::a('<h1 class="text-left" style="padding-left: 1%">'.Yii::t('buttons', 'Отчет остатки').'</h1>','/ecommerce/intermode/stock/report/index', ['class' => 'btn btn-lg btn-default btn-block text-large','style'=>"padding:0px"]) ?>
<?= \yii\helpers\Html::a('<h1 class="text-left" style="padding-left: 1%">'.Yii::t('buttons', 'Отчет приходы').'</h1>','/ecommerce/intermode/inbound/report/index', ['class' => 'btn btn-lg btn-default btn-block text-large','style'=>"padding:0px"]) ?>
<?= \yii\helpers\Html::a('<h1 class="text-left" style="padding-left: 1%">'.Yii::t('buttons', 'Отчет отгрузки').'</h1>','/ecommerce/intermode/outbound/report/index', ['class' => 'btn btn-lg btn-default btn-block text-large','style'=>"padding:0px"]) ?>
<?//= \yii\helpers\Html::a('<h1 class="text-left" style="padding-left: 1%">'.Yii::t('buttons', 'Отчет отгрузки по Дефакто данным').'</h1>','/ecommerce/defacto/report/outbound-by-defacto-orders', ['class' => 'btn btn-lg btn-default btn-block text-large','style'=>"padding:0px"]) ?>
<?//= \yii\helpers\Html::a('<h1 class="text-left" style="padding-left: 1%">'.Yii::t('buttons', 'Отчет по браку').'</h1>','/ecommerce/defacto/problem-report/index', ['class' => 'btn btn-lg btn-default btn-block text-large','style'=>"padding:0px"]) ?>
<?//= \yii\helpers\Html::a('<h1 class="text-left" style="padding-left: 1%">'.Yii::t('buttons', 'Отчет по возвратам').'</h1>','/ecommerce/defacto/return-report/index', ['class' => 'btn btn-lg btn-default btn-block text-large','style'=>"padding:0px"]) ?>
<?//= \yii\helpers\Html::a('<h1 class="text-left" style="padding-left: 1%">'.Yii::t('buttons', 'Отчет по трансферам').'</h1>','/ecommerce/defacto/transfer-report/index', ['class' => 'btn btn-lg btn-default btn-block text-large','style'=>"padding:0px"]) ?>
<?//= Html::a('<h1 class="text-left" style="padding-left: 1%">'.Yii::t('buttons', 'Отчет по трансферам Today').'</h1>','/ecommerce/defacto/transfer-report-to-day/index', ['class' => 'btn btn-lg btn-default btn-block text-large','style'=>"padding:0px"]) ?>
<?//= Html::a('<h1 class="text-left" style="padding-left: 1%">'.Yii::t('buttons', 'Отчет по размещениям').'</h1>','/ecommerce/defacto/change-address-place/report', ['class' => 'btn btn-lg btn-default btn-block text-large','style'=>"padding:0px"]) ?>
<?//= \yii\helpers\Html::a('<h1 class="text-left" style="padding-left: 1%">'.Yii::t('buttons', 'Найти товар на складе B2C').'</h1>','/ecommerce/defacto/report/find-product-on-stock', ['class' => 'btn btn-lg btn-default btn-block text-large','style'=>"padding:0px"]) ?>

<?//= '<h1 class="text-left btn btn-lg btn-default btn-block text-large" style="padding-left: 1%; padding-top:1%"></h1>'; ?>
<!---->
<?= \yii\helpers\Html::a('<h1 class="text-left" style="padding-left: 1%">'.Yii::t('buttons', 'Печать этикеток').'</h1>','/ecommerce/intermode/barcode/default/print-barcode', ['class' => 'btn btn-lg btn-default btn-block text-large','style'=>"padding:0px"]) ?>
<!---->
<?//= \yii\helpers\Html::a('<h1 class="text-left" style="padding-left: 1%">'.Yii::t('buttons', 'Inventory обработка короба').'</h1>','/ecommerce/defacto/check-box/scanning', ['class' => 'btn btn-lg btn-default btn-block text-large','style'=>"padding:0px"]) ?>
<?//= \yii\helpers\Html::a('<h1 class="text-left" style="padding-left: 1%">'.Yii::t('buttons', 'Inventory отчет по коробам').'</h1>','/ecommerce/defacto/check-box/index', ['class' => 'btn btn-lg btn-default btn-block text-large','style'=>"padding:0px"]) ?>
<?//= \yii\helpers\Html::a('<h1 class="text-left" style="padding-left: 1%">'.Yii::t('buttons', 'Inventory отчет').'</h1>','/ecommerce/defacto/check-box-inventory/index', ['class' => 'btn btn-lg btn-default btn-block text-large','style'=>"padding:0px"]) ?>
<!---->
<?//= '<h1 class="text-left btn btn-lg btn-default btn-block text-large" style="padding-left: 1%; padding-top:1%"></h1>'; ?>
<?//= '<h1 class="text-left btn btn-lg btn-default btn-block text-large" style="padding-left: 1%; padding-top:1%"></h1>'; ?>
<!---->
<?//= \yii\helpers\Html::a('<h1 class="text-left" style="padding-left: 1%">'.Yii::t('buttons', 'Inventory полный старт').'</h1>','/ecommerce/defacto/inventory/index', ['class' => 'btn btn-lg btn-danger btn-block text-large','style'=>"padding:0px"]) ?>
<?//= \yii\helpers\Html::a('<h1 class="text-left" style="padding-left: 1%">'.Yii::t('buttons', 'Inventory полный сканируем').'</h1>','/ecommerce/defacto/inventory-process/index', ['class' => 'btn btn-lg btn-warning btn-block text-large','style'=>"padding:0px"]) ?>
