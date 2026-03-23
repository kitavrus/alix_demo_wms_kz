<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\stock\models\StockSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="stock-search">

    <?php $form = ActiveForm::begin([
        'action' => ['where-from-box'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'primary_address') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('forms', 'Найти'), ['class' => 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>