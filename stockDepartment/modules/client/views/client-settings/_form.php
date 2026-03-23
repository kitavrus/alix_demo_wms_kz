<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\client\models\ClientSettings */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="client-settings-form">

    <?php $form = ActiveForm::begin(); ?>


    <?= $form->field($model, 'option_name')->textInput() ?>

    <?= $form->field($model, 'option_value')->dropDownList($model->getOptionsList(), ['prompt'=>'']) ?>

    <?= $form->field($model, 'default_value')->textInput() ?>

    <?= $form->field($model, 'option_type')->dropDownList($model->getOptionType(), ['prompt'=>'']) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>


    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('buttons', 'Create') : Yii::t('buttons', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script type="text/javascript">

    $(function() {

        var b = $('body');

        b.on('change','#clientsettings-option_value',function() {
            $.post(
                'get-options-by-value',
                {'option_name':$(this).val()}
            )
                .done(function (result) {
                    var options='';
                    $.each(result.data_options, function (key, value) {
                         options += '<option value='+key+'>'+value+'</option>';

                        // $('#tldeliveryproposal-car_id').append('<option value="' + key + '">' + value + '</option>');
                        $("#clientsettings-default_value")
                            .replaceWith('<select id="clientsettings-default_value" name="ClientSettings[default_value]" class="form-control">' +
                            options+
                            '</select>');
                    });

                })
                .fail(function () {
                    console.log("server error");
                });
        });



    });
</script>
