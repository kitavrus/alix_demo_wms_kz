<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\daterange\DateRangePicker;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model common\ecommerce\entities\EcommerceOutboundSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ecommerce-inventory-search">

    <?php $form = ActiveForm::begin([
        'id' => 'inventory-search-form',
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <table class="table" width="100%" cellspacing="10">
        <tr>
            <td width="10%">
                <?= $form->field($model, 'inventory_id')->dropDownList($allInventoryKeyList, ['prompt' => '']); ?>
            </td>
            <td width="10%">
                <?= $form->field($model, 'box_barcode')->label(Yii::t('Inventory/forms', 'Box barcode')) ?>
            </td>
            <td width="10%">
                <?= $form->field($model, 'place_address')->label(Yii::t('Inventory/forms', 'Place barcode')) ?>
            </td>
        </tr>
    </table>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('buttons', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('buttons', 'Очистить поиск'), 'index', ['class' => 'btn btn-warning']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>