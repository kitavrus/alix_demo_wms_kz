<?php
use yii\helpers\Html;
?>
<h1 class="text-center">Ecommerce</h1>

<h2 class="text-left" style="padding-left: 1%; margin-top: 30px;">Приход</h2>
<?= Html::a('<h1 class="text-left" style="padding-left: 1%">'.Yii::t('buttons', 'Приёмка товара').'</h1>','/alix/ecommerce/inbound/scanning/index', ['class' => 'btn btn-lg btn-success btn-block','style'=>"padding:0px"]) ?>

<h2 class="text-left" style="padding-left: 1%; margin-top: 30px;">Отгрузка</h2>
<?= Html::a('<h1 class="text-left" style="padding-left: 1%">'.Yii::t('buttons', 'ЛИСТЫ СБОРКИ').'</h1>','/alix/ecommerce/outbound/picking/lists', ['class' => 'btn btn-lg btn-danger btn-block','style'=>"padding:0px"]) ?>
<?= Html::a('<h1 class="text-left" style="padding-left: 1%">'.Yii::t('buttons', 'Сканирование сборки').'</h1>','/alix/ecommerce/outbound/scanning/scanning-form', ['class' => 'btn btn-lg btn-success btn-block','style'=>"padding:0px"]) ?>
<?= Html::a('<h1 class="text-left" style="padding-left: 1%">'.Yii::t('buttons', 'Лист отгрузки').'</h1>','/alix/ecommerce/outbound/outbound-list/scanning-form', ['class' => 'btn btn-lg btn-default btn-block text-large','style'=>"padding:0px"]) ?>
<?= Html::a('<h1 class="text-left" style="padding-left: 1%">'.Yii::t('buttons', 'Отчет отгрузки').'</h1>','/alix/ecommerce/outbound/report/index', ['class' => 'btn btn-lg btn-default btn-block text-large','style'=>"padding:0px"]) ?>

<h2 class="text-left" style="padding-left: 1%; margin-top: 30px;">Возвраты</h2>
<?= Html::a('<h1 class="text-left" style="padding-left: 1%">'.Yii::t('buttons', 'Создать возврат').'</h1>','/alix/ecommerce/returns/scanning/scanning-returns', ['class' => 'btn btn-lg btn-success btn-block','style'=>"padding:0px"]) ?>
<?= Html::a('<h1 class="text-left" style="padding-left: 1%">'.Yii::t('buttons', 'Обработка возвратов').'</h1>','/alix/ecommerce/returns/scanning/index', ['class' => 'btn btn-lg btn-success btn-block','style'=>"padding:0px"]) ?>
