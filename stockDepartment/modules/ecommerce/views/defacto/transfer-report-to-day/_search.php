<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $model common\ecommerce\entities\EcommerceStockSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ecommerce-transfer-search">
    <?php $form = ActiveForm::begin([
        'id' => 'transfer-order-search-to-day-form',
        'action' => ['index'],
        'method' => 'get',
    ]);
    ?>

    <table class="table" width="100%" cellspacing="10">
        <tr>
            <td width="10%">
				<?= $form->field($model, 'transfer_id')->dropDownList($model->getLastTransfer(), ['prompt'=>'Выберите трансфер']) ?>
            </td>
            <td width="10%">
                <?= $form->field($model, 'product_barcode') ?>
            </td>
            <td width="10%">
                <?= $form->field($model, 'transfer_outbound_box') ?>
            </td>
<!--            <td width="10%">-->
<!--                --><?//= $form->field($model, 'status_transfer')->dropDownList(\common\ecommerce\constants\StockTransferStatus::getAll(), ['prompt'=>'Выберите статус']) ?>
<!--            </td>-->
        <tr>
        <tr>
            <td width="10%">
                <?= $form->field($model, 'scan_out_datetime')->widget(DateRangePicker::className(),
                    [
                        'convertFormat'=>true,
                        'pluginOptions'=>[
                            'locale'=>[
                                'separator'=> ' / ',
                                'format'=>'Y-m-d',
                            ]
                        ]
                    ]
                ) ?>
            </td>
        <tr>
    </table>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('buttons', 'Очистить поиск'), 'index', ['class' => 'btn btn-warning']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
