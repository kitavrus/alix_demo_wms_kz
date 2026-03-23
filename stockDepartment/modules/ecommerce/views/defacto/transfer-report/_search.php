<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $model common\ecommerce\entities\EcommerceTransferSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ecommerce-transfer-search">
    <?php $form = ActiveForm::begin([
        'id' => 'transfer-order-search-form',
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    <table class="table" width="100%" cellspacing="10">
        <tr>
            <td width="10%">
                <?= $form->field($model, 'id') ?>
            </td>
            <td width="10%">
                <?= $form->field($model, 'client_BatchId') ?>
            </td>
            <td width="10%">
                <?= $form->field($model, 'expected_box_qty') ?>
            </td>
            <td width="10%">
                <?= $form->field($model, 'status')->dropDownList(\common\ecommerce\constants\TransferStatus::getAll(), ['prompt'=>'Выберите статус']) ?>
            </td>
        <tr>
        <tr>
            <td width="10%">
                <?= $form->field($model, 'print_picking_list_date')->widget(DateRangePicker::className(),
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
                <?= $form->field($model, 'packing_date')->widget(DateRangePicker::className(),
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
                <?= $form->field($model, 'date_left_warehouse')->widget(DateRangePicker::className(),
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
				<?= $form->field($model, 'boxBarcode') ?>
		    </td>
        <tr>

    </table>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('buttons', 'Очистить поиск'), 'index', ['class' => 'btn btn-warning']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
