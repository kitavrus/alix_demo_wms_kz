<?php
/**
 * Created by PhpStorm.
 * User: Kitavrus
 * Date: 07.10.2017
 * Time: 17:55
 */
?>
<?= \yii\helpers\Html::a('<h1>'.Yii::t('buttons', 'Отчет приходы <').'</h1>',['/wms/carParts/main/for-manager/inbound'], ['class' => 'btn btn-lg btn-default btn-block text-large','style'=>"padding:20px"]) ?>
<?= \yii\helpers\Html::a('<h1>'.Yii::t('buttons', 'Отчет отгрузки >').'</h1>',['/wms/carParts/main/for-manager/outbound'], ['class' => 'btn btn-lg btn-default btn-block text-large','style'=>"padding:20px"]) ?>
<?= \yii\helpers\Html::a('<h1>'.Yii::t('buttons', 'Восстановить поврежденный товар').'</h1>',['/wms/carParts/main/for-manager/damage-stock'], ['class' => 'btn btn-lg btn-default btn-block text-large','style'=>"padding:20px"]) ?>