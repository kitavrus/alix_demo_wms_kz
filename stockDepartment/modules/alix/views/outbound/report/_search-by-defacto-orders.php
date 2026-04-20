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
        'action' => ['outbound-by-defacto-orders'],
        'method' => 'post',
    ]); ?>

    <table class="table" width="100%" cellspacing="10">
        <tr>
<!--            <td width="10%">-->
<!--                --><?//= $form->field($model, 'client_ReferenceNumber')->label(Yii::t('outbound/forms', 'ТТН')); ?>
<!--            </td>-->
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
                <?= $form->field($model, 'updated_at')->widget(DateRangePicker::className(),
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
                <?= $form->field($model, 'client_ShipmentSource')->dropDownList(\common\ecommerce\constants\OutboundShipmentSource::getAll(), ['prompt'=>'Выберите статус']) ?>
            </td>
        </tr>
        <tr>
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
                <?= $form->field($model, 'status')->dropDownList(\common\ecommerce\constants\OutboundStatus::getAll(), ['prompt'=>'Выберите статус']) ?>
            </td>
            <td width="10%">
                <?= $form->field($model, 'productBarcode')->label('Продукт штрих-код') ?>
            </td>
            <td width="10%">
                <?= $form->field($model, 'id')->label(Yii::t('outbound/forms', 'Id')) ?>
            </td>
            <td width="10%">
                <?= $form->field($model, 'outboundBoxBarcode')->label(Yii::t('outbound/forms', 'Шк короба отгрузки')) ?>
            </td>

        </tr>
        <tr>
            <td width="100%" colspan="6">
                <?= $form->field($model, 'order_number')->textarea()->label(Yii::t('outbound/forms', 'Order number')) ?>
            </td>
        </tr>
        <tr>
            <td width="100%" colspan="6">
                <?= $form->field($model, 'client_ReferenceNumber')->textarea()->label(Yii::t('outbound/forms', 'ТТН')) ?>
            </td>
        </tr>

    </table>

    <div class="form-group">
        <?= Html::button(Yii::t('buttons', 'Search'), ['class' => 'btn btn-primary','id'=>'search-bt']) ?>
<!--        --><?//= Html::submitButton(Yii::t('buttons', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('buttons', 'Очистить поиск'), 'outbound-by-defacto-orders', ['class' => 'btn btn-warning']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<script type="text/javascript">
    $(function(){

        $('#search-bt').on('click',function() {
            $('#outbound-order-search-form').attr('action','outbound-by-defacto-orders');
            $('#outbound-order-search-form').submit();
        });
    });
</script>