<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $model common\ecommerce\entities\EcommerceInboundSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ecommerce-inbound-search">

    <?php $form = ActiveForm::begin([
        'id' => 'inbound-order-search-form',
        'action' => ['inbound'],
        'method' => 'get',
    ]); ?>

    <table class="table" width="100%" cellspacing="10">
        <tr>
            <td width="10%">
                <?= $form->field($model, 'id')->label(Yii::t('outbound/forms', 'ID')) ?>
            </td>
            <td width="10%">
                <?= $form->field($model, 'order_number')->label(Yii::t('outbound/forms', 'Order number')) ?>
            </td>
            <td width="10%">
                <?= $form->field($model, 'created_at')->widget(DateRangePicker::className(),
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
            <td width="10%">
                <?= $form->field($model, 'status')->dropDownList(\common\ecommerce\constants\InboundStatus::getAll(), ['prompt'=>'Выберите статус']) ?>
            </td>
        </tr>
		<tr>
            <td width="10%">
                <?= $form->field($model, 'clientBoxBarcode')->label(Yii::t('outbound/forms', 'Короб клиента')) ?>
            </td>
            <td width="10%">
                <?= $form->field($model, 'ourBoxBarcode')->label(Yii::t('outbound/forms', 'Наш короб')) ?>
            </td>
        </tr>
    </table>

<!--    --><?//= $form->field($model, 'id') ?>

<!--    --><?//= $form->field($model, 'client_id') ?>

<!--    --><?//= $form->field($model, 'party_number') ?>
<!---->
<!--    --><?//= $form->field($model, 'order_number') ?>

<!--    --><?//= $form->field($model, 'expected_box_qty') ?>

    <?php // echo $form->field($model, 'accepted_box_qty') ?>

    <?php // echo $form->field($model, 'expected_lot_qty') ?>

    <?php // echo $form->field($model, 'accepted_lot_qty') ?>

    <?php // echo $form->field($model, 'expected_product_qty') ?>

    <?php // echo $form->field($model, 'accepted_product_qty') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'begin_datetime') ?>

    <?php // echo $form->field($model, 'end_datetime') ?>

    <?php // echo $form->field($model, 'date_confirm') ?>

    <?php // echo $form->field($model, 'created_user_id') ?>

    <?php // echo $form->field($model, 'updated_user_id') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'deleted') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('buttons', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('buttons', 'Очистить поиск'), 'inbound', ['class' => 'btn btn-warning']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
