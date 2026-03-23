<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>

<?php $this->title = Yii::t('return/titles', 'Miele movement orders'); ?>
<span id="buttons-menu">
    <?= Html::a( Yii::t('wms/buttons', 'Print pick list [{0}] Miele', ['1']),Url::toRoute('picking-list'), [
        'class' => 'btn btn-primary btn-lg',
        'style' => ' margin:10px;',
    ]) ?>

    <?= Html::a( Yii::t('wms/buttons', 'Begin end picking process [{0}] Miele', ['2']),Url::toRoute('begin-end-picking-handler'), [
        'class' => 'btn btn-warning btn-lg',
        'style' => ' margin:10px;',
    ]) ?>

    <?= Html::a( Yii::t('wms/buttons', 'Outbound scanning process [{0}] Miele', ['3']),Url::toRoute('scanning-form'), [
        'class' => 'btn btn-danger btn-lg',
        'style' => ' margin:10px;',
    ]) ?>
</span>