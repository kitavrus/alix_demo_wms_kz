<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model stockDepartment\modules\kpiSettings\models\KpiSettingSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="stock-item-search">
    <?php $form = ActiveForm::begin([
        'action' => [''],
        'method' => 'get',
        'id' => 'make-defect-search-form',
    ]); ?>

    <?= $form->field($model, 'primary_address') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('buttons', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('buttons', 'Очистить поиск'), [''], ['class' => 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>