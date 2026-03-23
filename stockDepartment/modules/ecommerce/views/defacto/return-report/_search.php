<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $model common\ecommerce\entities\EcommerceReturnSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ecommerce-return-report-search">

    <?php $form = ActiveForm::begin([
        'id' => 'return-report-order-search-form',
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <table class="table" width="100%" cellspacing="10">
        <tr>
            <td width="10%">
                <?= $form->field($model, 'order_number')->label(Yii::t('outbound/forms', 'Order number')) ?>
            </td>
	        <td width="10%">
                <?= $form->field($model, 'outbound_box')->label(Yii::t('outbound/forms', 'Outbound box')) ?>
            </td>
	        <td width="10%">
                <?= $form->field($model, 'client_ExternalOrderId')->label(Yii::t('outbound/forms', 'Client ExternalOrderId')) ?>
            </td>
	        <td width="10%">
                <?= $form->field($model, 'client_ReferenceNumber')->label(Yii::t('outbound/forms', 'Client ReferenceNumber')) ?>
            </td>
	        <td width="10%">
                <?= $form->field($model, 'client_OrderSource')->label(Yii::t('outbound/forms', 'Client OrderSource')) ?>
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
        </tr>
        <tr>
            <td width="10%">
                <?= $form->field($model, 'status')->dropDownList(\common\ecommerce\constants\ReturnOutboundStatus::getAll(), ['prompt'=>'Выберите статус']) ?>
            </td>
            <td width="10%">
                <?= $form->field($model, 'id')->label(Yii::t('outbound/forms', 'Id')) ?>
            </td>
        </tr>
    </table>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('buttons', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('buttons', 'Очистить поиск'), 'index', ['class' => 'btn btn-warning']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>