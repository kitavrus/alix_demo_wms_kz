<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>

<?php $this->title = Yii::t('return/titles', 'Hyundai Auto'); ?>
<span id="buttons-menu">
    <?= Html::a( Yii::t('wms/buttons', 'Печатаем лист сборки', ['1']),Url::toRoute('picking-list'), [
        'class' => 'btn btn-primary btn-lg',
        'style' => ' margin:10px;',
    ]) ?>

    <?= Html::a( Yii::t('wms/buttons', 'Фиксируем начало и окончание сборки', ['2']),Url::toRoute('begin-end-picking-handler'), [
        'class' => 'btn btn-warning btn-lg',
        'style' => ' margin:10px;',
    ]) ?>

    <?= Html::a( Yii::t('wms/buttons', 'Сканирование (отгрузка)', ['3']),Url::toRoute('scanning-form'), [
        'class' => 'btn btn-danger btn-lg',
        'style' => ' margin:10px;',
    ]) ?>
</span>