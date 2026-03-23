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
<?= \yii\helpers\Html::a('<h1 class="text-left" style="padding-left: 1%">'.Yii::t('buttons', 'ЛИСТЫ СБОРКИ').'</h1>','/intermode/ecommerce/outbound/picking/lists', ['class' => 'btn btn-lg btn-danger btn-block','style'=>"padding:0px"]) ?>
<?= \yii\helpers\Html::a('<h1 class="text-left" style="padding-left: 1%">'.Yii::t('buttons', 'Обработка расходов').'</h1>','/intermode/ecommerce/outbound/scanning/index', ['class' => 'btn btn-lg btn-success btn-block','style'=>"padding:0px"]) ?>
<?= \yii\helpers\Html::a('<h1 class="text-left" style="padding-left: 1%">'.Yii::t('buttons', 'Отчет отгрузки').'</h1>','/intermode/ecommerce/outbound/report/index', ['class' => 'btn btn-lg btn-default btn-block text-large','style'=>"padding:0px"]) ?>
<?= \yii\helpers\Html::a('<h1 class="text-left" style="padding-left: 1%">'.Yii::t('buttons', 'Печать этикеток').'</h1>','/intermode/ecommerce/barcode/default/print-barcode', ['class' => 'btn btn-lg btn-default btn-block text-large','style'=>"padding:0px"]) ?>
<?= \yii\helpers\Html::a('<h1 class="text-left" style="padding-left: 1%">'.Yii::t('buttons', 'Загрузить заказы KASPI').'</h1>','/intermode/ecommerce/outbound/uploads/form', ['class' => 'btn btn-lg btn-danger btn-block text-large','style'=>"padding:0px"]) ?>
