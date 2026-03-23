<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\daterange\DateRangePicker;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model common\ecommerce\entities\EcommerceOutboundSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ecommerce-outbound-search">

    <?php $form = ActiveForm::begin([
        'id' => 'on-stock-form',
        'action' => ['on-stock'],
        'method' => 'get',
    ]); ?>

    <table class="table" width="100%" cellspacing="10">
        <tr>
            <td width="10%">
                <?= $form->field($model, 'product_barcode'); ?>
            </td>
            <td width="10%">
                <?= $form->field($model, 'place_address_barcode'); ?>
            </td>
            <td width="10%">
                <?= $form->field($model, 'box_address_barcode'); ?>
            </td>
            <td width="10%">
                <?= $form->field($model, 'condition_type')->dropDownList((new \common\ecommerce\constants\StockConditionType)->getConditionTypeArray(),['prompt' =>'Select...']); ?>
            </td>

        </tr>
    </table>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('buttons', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('buttons', 'Очистить поиск'), 'on-stock', ['class' => 'btn btn-warning']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>