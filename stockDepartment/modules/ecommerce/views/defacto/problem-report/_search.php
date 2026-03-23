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
        'id' => 'outbound-order-search-form',
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <table class="table" width="100%" cellspacing="10">
        <tr>
            <td width="10%">
                <?= $form->field($model, 'product_barcode')->label('Продукт штрих-код') ?>
            </td>
            <td width="10%">
                <?= $form->field($model, 'order_re_reserved')->label('В каком заказе перерезерв') ?>
            </td>
            <td width="10%">
                <?= $form->field($model, 'reason_re_reserved')->dropDownList(\common\ecommerce\constants\OutboundCancelStatus::getForPartReReservedList(), ['prompt'=>'Выберите статус']) ?>
            </td>
            <td width="10%">
                <?= $form->field($model, 'condition_type')->dropDownList(( new \common\ecommerce\constants\StockConditionType)->getConditionTypeArray(), ['prompt'=>'Выберите ...'])->label(Yii::t('outbound/forms', 'Состояние товара')) ?>
            </td>
        </tr>

        <tr>
            <td width="10%">
                <?= $form->field($model, 'place_address_barcode')->label(Yii::t('outbound/forms', 'place address barcode')) ?>
            </td>
            <td width="10%">
                <?= $form->field($model, 'box_address_barcode')->label(Yii::t('outbound/forms', 'box address barcode')); ?>
            </td>
            <td width="10%">
                -
            </td>
            <td width="10%">
                -
            </td>
        </tr>

    </table>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('buttons', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('buttons', 'Очистить поиск'), 'index', ['class' => 'btn btn-warning']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>