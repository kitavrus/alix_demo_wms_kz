<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\modules\transportLogistics\models\TlAgents;
use kartik\select2\Select2;
use common\modules\transportLogistics\models\TlCars;

/* @var $this yii\web\View */
/* @var $model common\modules\transportLogistics\models\TlOutboundRegistry */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tl-outbound-registry-form">
    <?php if($model->isNewRecord){
        echo Html::label('ШК субподрядчика','agent_barcode').Html::textInput('agent_barcode','',['class'=>'form-control input-lg', 'id'=>'agent_barcode'])."<br>";
    } ?>
    <?php $form = ActiveForm::begin([
        'enableClientValidation' => false,
        'enableAjaxValidation' => false,
    ]); ?>

    <?= $form->field($model, 'agent_id')->widget(Select2::classname(), [
        'language' => 'ru',
        'data' => TlAgents::getActiveAgentsArray(),
        'options' => ['placeholder' => Yii::t('transportLogistics/forms','Please select the shipping company')],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]);
    ?>

    <?= $form->field($model, 'car_id')->dropDownList($model->isNewRecord ? [] : TlCars::getCarArray()) ?>

    <?= $form->field($model, 'driver_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'driver_phone')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'driver_auto_number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'price_invoice')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'price_invoice_with_vat')->textInput(['maxlength' => true]) ?>
<!---->
<!--    --><?//= $form->field($model, 'places')->textInput() ?>
<!---->
<!--    --><?//= $form->field($model, 'extra_fields')->textarea(['rows' => 6]) ?>
<!---->
<!--    --><?//= $form->field($model, 'created_user_id')->textInput() ?>
<!---->
<!--    --><?//= $form->field($model, 'updated_user_id')->textInput() ?>
<!---->
<!--    --><?//= $form->field($model, 'created_at')->textInput() ?>
<!---->
<!--    --><?//= $form->field($model, 'updated_at')->textInput() ?>
<!---->
<!--    --><?//= $form->field($model, 'deleted')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('buttons', 'Create') : Yii::t('buttons', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary', 'id' => 'submit-registry-form']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script type="text/javascript">


    $(function() {
        var aBarcode = $('#agent_barcode'),
            b = $('body');
        aBarcode.focus().select();

        aBarcode.on('click',function() {
            aBarcode.focus().select();
        });

        b.on('keyup', aBarcode, function (e) {

            if (e.which == 13) {

               $('#tloutboundregistry-agent_id').val(parseInt(e.target.value));
                $('#tloutboundregistry-agent_id').trigger('change');
            }

            e.preventDefault();
        });


        b.on('change','#tloutboundregistry-agent_id',function() {
            $.post(
                '/tms/default/get-cars-by-agent',
                {'agent_id':$(this).val()}
            )
                .done(function (result) {

                    $('#tloutboundregistry-car_id').html('');

                    $.each(result.data_options, function (key, value) {
                        $('#tloutboundregistry-car_id').append('<option value="' + key + '">' + value + '</option>');
                    });

                })
                .fail(function () {
                    console.log("server error");
                });
        });

    });

</script>
