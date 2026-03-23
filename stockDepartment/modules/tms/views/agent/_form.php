<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\transportLogistics\transportLogistics;
use common\modules\transportLogistics\components\TLHelper;


/* @var $this yii\web\View */
/* @var $model common\modules\transportLogistics\models\TlAgents */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tl-agents-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 128]) ?>

<!--    --><?//= $form->field($model, 'title')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'phone')->textInput(['maxlength' => 64]) ?>

    <?= $form->field($model, 'phone_mobile')->textInput(['maxlength' => 64]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
<!---->
<!--    --><?//= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'status')->dropDownList( $model::getStatusArray()); ?>

    <?= $form->field($model, 'payment_period')->dropDownList( $model::getPaymentPeriodArray()); ?>

    <?= $form->field($model, 'flag_nds')->dropDownList( $model::getNdsFlagArray()); ?>

    <?= $form->field($model, 'contact_first_name')->textInput(['maxlength' => 64]) ?>

    <?= $form->field($model, 'contact_middle_name')->textInput(['maxlength' => 64]) ?>

    <?= $form->field($model, 'contact_last_name')->textInput(['maxlength' => 64]) ?>

    <?= $form->field($model, 'contact_phone')->textInput(['maxlength' => 64]) ?>

    <?= $form->field($model, 'contact_phone_mobile')->textInput(['maxlength' => 64]) ?>

    <?= $form->field($model, 'contact_first_name2')->textInput(['maxlength' => 64]) ?>

    <?= $form->field($model, 'contact_middle_name2')->textInput(['maxlength' => 64]) ?>

    <?= $form->field($model, 'contact_last_name2')->textInput(['maxlength' => 64]) ?>

    <?= $form->field($model, 'contact_phone2')->textInput(['maxlength' => 64]) ?>

    <?= $form->field($model, 'contact_phone_mobile2')->textInput(['maxlength' => 64]) ?>

<!--    --><?//= $form->field($model, 'address_title')->textInput(['maxlength' => 256]) ?>

    <?= $form->field($model, 'country_id')->dropDownList(TLHelper::getCountryArray(), ['prompt' =>Yii::t('transportLogistics/titles', 'Select country')]) ?>
    <?= $form->field($model, 'region_id')->dropDownList(TLHelper::getRegionArray(), ['prompt' =>Yii::t('transportLogistics/titles', 'Select region')]) ?>
    <?= $form->field($model, 'city_id')->dropDownList(TLHelper::getCityArray(), ['prompt' =>Yii::t('transportLogistics/titles', 'Select city')]) ?>

    <?= $form->field($model, 'zip_code')->textInput(['maxlength' => 9]) ?>

    <?= $form->field($model, 'street')->textInput(['maxlength' => 128]) ?>

    <?= $form->field($model, 'house')->textInput(['maxlength' => 6]) ?>

    <?= $form->field($model, 'entrance')->textInput(['maxlength' => 6]) ?>

    <?= $form->field($model, 'flat')->textInput(['maxlength' => 6]) ?>

    <?= $form->field($model, 'intercom')->textInput() ?>

    <?= $form->field($model, 'floor')->textInput() ?>

    <?= $form->field($model, 'comment')->textarea(['rows' => 6]) ?>

<!--    --><?//= $form->field($model, 'created_at')->textInput() ?>

<!--    --><?//= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('transportLogistics/buttons', 'Create') : Yii::t('transportLogistics/buttons', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script type="text/javascript">

    $(function() {
        var b = $('body');

        b.on('change','#tlagents-region_id',function() {
            $.post(
//                '/transportLogistics/agent/get-city-by-region',
                '/city/default/get-city-by-region',
                {'region_id':$(this).val()}
            )
                .done(function (result) {

                    $('#tlagents-city_id').html('');
                    $.each(result.data_options, function (key, value) {

                        $('#tlagents-city_id').append('<option value="' + key + '">' + value + '</option>');
                    });
//                    $('#tlagents-city_id :last').attr('selected','selected');
                })
                .fail(function () {
                    console.log("server error");
                });
        });

        b.on('change','#tlagents-country_id',function() {
            $.post(
//                '/transportLogistics/agent/get-region-by-country',
                '/city/default/get-region-by-country',
                {'country_id':$(this).val()}
            )
                .done(function (result) {

                    $('#tlagents-region_id').html('');

                    console.info(result.data_options);

                    $.each(result.data_options, function (key, value) {

                        $('#tlagents-region_id').append('<option value="' + key + '">' + value + '</option>');
                    });
                    $('#tlagents-region_id :last').attr('selected','selected');
                    $('#tlagents-city_id').html('');

                })
                .fail(function () {
                    console.log("server error");
                });
        });

    });

</script>
