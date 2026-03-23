<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model stockDepartment\modules\kpiSettings\models\KpiSettingSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="outbound-orders-grid-search">

    <?php $form = ActiveForm::begin([
        'method' => 'get',
        'id' => 'outbound-orders-grid-search-form',
        'options' => [
            'style' => 'padding-top:15px;padding-bottom:15px;',
        ],
    ]); ?>

    <!-- <?= $form
        ->field($model, 'order_number')
        ->label(Yii::t('outbound/forms', 'Order number')) ?> -->

    <table class="table" width="100%" cellspacing="10">
        <tr>
            <td width="10%">
                <?= $form->field($model, 'order_number')->label(Yii::t('outbound/forms', 'Order number')) ?>
            </td>
            <td width="10%">
                <?= $form->field($model, 'product_barcode')->label(Yii::t('stock/forms', 'Product barcode')) ?>
            </td>
        </tr>
    </table>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('buttons', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('buttons', 'Очистить поиск'), 'index', ['class' => 'btn btn-warning']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>