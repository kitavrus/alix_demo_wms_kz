<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\daterange\DateRangePicker;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model app\modules\stock\models\StockBindReport */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="stock-item-search">

    <?php $form = ActiveForm::begin([
        'action' => ['bind-qr-code-report'],
        'method' => 'get',
        'id' => 'bind-qr-code-report-search-form',
    ]); ?>

    <table class="table" width="100%" cellspacing="10">
        <tr>
            <td width="15%">
                <?= $form->field($model, 'client_id')->dropDownList($clientsArray, ['prompt' => Yii::t('transportLogistics/titles', 'Select client')]) ?>
            </td>
            <td width="15%">
                <?= $form->field($model, 'condition_type')->dropDownList($conditionTypeArray, ['prompt' => Yii::t('transportLogistics/titles', 'Select client')]) ?>
            </td>
            <td width="15%">
                <?= $form->field($model, 'parent_order_number') ?>
            </td>
            <td width="15%">
                <?= $form->field($model, 'product_barcode') ?>
            </td>
            <td width="15%">
                <?= $form->field($model, 'primary_address') ?>
            </td>
            <td width="15%">
                <?= $form->field($model, 'secondary_address') ?>
            </td>
            <td width="15%">
                <?= $form->field($model, 'product_model') ?>
            </td>
        </tr>
        <tr>
            <td width="15%">
                <?= $form->field($model, 'our_product_barcode') ?>
            </td>
            <td width="15%">
                <?= $form->field($model, 'bind_qr_code') ?>
            </td>
        </tr>
    </table>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('buttons', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('buttons', 'Очистить поиск'), ['bind-qr-code-report'], ['class' => 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>