<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use app\modules\transportLogistics\transportLogistics;
use kartik\datecontrol\DateControl;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;

use common\modules\transportLogistics\components\TLHelper;
use common\modules\client\models\Client;

/* @var $this yii\web\View */
/* @var $model common\modules\billing\models\TlDeliveryProposalBilling */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tl-delivery-proposal-billing-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'client_id')->dropDownList(ArrayHelper::map(Client::findAll(['status' => Client::STATUS_ACTIVE]), 'id', 'title'),['prompt' =>Yii::t('titles', 'Select client')]); ?>
    <?=
    $form->field($model, 'route_from',[])->widget(Select2::classname(), [
        'language' => 'ru',
        'data' => TLHelper::getStockPointArray($model->client_id),
        'options' => ['placeholder' => Yii::t('titles', 'Select route from')],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]);
    ?>

    <?=
    $form->field($model, 'route_to',[])->widget(Select2::classname(), [
        'language' => 'ru',
        'data' => TLHelper::getStockPointArray($model->client_id),
        'options' => ['placeholder' => Yii::t('titles', 'Select route to')],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]);
    ?>

    <?= $form->field($model, 'from_country_id')->dropDownList(TLHelper::getCountryArray(), ['prompt' =>Yii::t('titles', 'Select country')]) ?>
    <?= $form->field($model, 'from_region_id')->dropDownList(TLHelper::getRegionArray(), ['prompt' =>Yii::t('titles', 'Select region')]) ?>
    <?= $form->field($model, 'from_city_id')->dropDownList(TLHelper::getCityArray(), ['prompt' =>Yii::t('titles', 'Select city')]) ?>

<!--    --><?//= $form->field($model, 'to_country_id')->dropDownList(TLHelper::getCountryArray(), ['prompt' =>Yii::t('titles', 'Select country')]) ?>
<!--    --><?//= $form->field($model, 'to_region_id')->dropDownList(TLHelper::getRegionArray(), ['prompt' =>Yii::t('titles', 'Select region')]) ?>
    <?= $form->field($model, 'to_city_id')->dropDownList(TLHelper::getCityArray(), ['prompt' =>Yii::t('titles', 'Select city')]) ?>

<!--    --><?//= $form->field($model, 'mc')->textInput(['maxlength' => 26]) ?>
<!--    --><?//= $form->field($model, 'kg')->textInput(['maxlength' => 26]) ?>
<!--    --><?//= $form->field($model, 'number_places')->textInput() ?>
<!--    --><?//= $form->field($model, 'price_invoice')->textInput(['maxlength' => 26]) ?>
    <?= $form->field($model, 'price_invoice_with_vat')->textInput(['maxlength' => 26]) ?>
    <?= $form->field($model, 'price_invoice_kg')->textInput(['maxlength' => 26]) ?>
    <?= $form->field($model, 'price_invoice_kg_with_vat')->textInput(['maxlength' => 26]) ?>
    <?= $form->field($model, 'price_invoice_mc')->textInput(['maxlength' => 26]) ?>
    <?= $form->field($model, 'price_invoice_mc_with_vat')->textInput(['maxlength' => 26]) ?>

<!--    --><?//= $form->field($model, 'formula_tariff')->textInput(['maxlength' => 256]) ?>
    <?= $form->field($model, 'rule_type')->dropDownList($model::getRuleTypeArray()) ?>
    <?= $form->field($model, 'status')->dropDownList($model->getStatusArray()) ?>
<!--    --><?php //echo $form->field($model, 'delivery_term')->textInput() ?>
    <?= $form->field($model, 'delivery_term_from')->textInput() ?>
    <?= $form->field($model, 'delivery_term_to')->textInput() ?>
    <?= $form->field($model, 'tariff_type')->dropDownList($model::getTariffTypeArray()) ?>
    <?= $form->field($model, 'cooperation_type')->dropDownList($model::getCooperationTypeArray()) ?>
    <?= $form->field($model, 'delivery_type')->dropDownList($model::getDeliveryTypeArray()) ?>

    <?= $form->field($model, 'comment')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('buttons', 'Create') : Yii::t('buttons', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>

</div>


<script type="text/javascript">

    $(function() {

        var b = $('body');

        b.on('change','#tldeliveryproposalbilling-region_id',function() {
            $.post(
                '/city/default/get-city-by-region',
                {'region_id':$(this).val()}
            )
                .done(function (result) {

                    $('#tldeliveryproposalbilling-city_id').html('');
                    $.each(result.data_options, function (key, value) {

                        $('#tldeliveryproposalbilling-city_id').append('<option value="' + key + '">' + value + '</option>');
                    });
                })
                .fail(function () {
                    console.log("server error");
                });
        });

        b.on('change','#tldeliveryproposalbilling-country_id',function() {
            $.post(
                '/city/default/get-region-by-country',
                {'country_id':$(this).val()}
            )
                .done(function (result) {

                    $('#tldeliveryproposalbilling-region_id').html('');
                    $.each(result.data_options, function (key, value) {

                        $('#tldeliveryproposalbilling-region_id').append('<option value="' + key + '">' + value + '</option>');
                    });
                })
                .fail(function () {
                    console.log("server error");
                });
        });


        b.on('change','#tldeliveryproposalbilling-client_id',function() {

            $.post(
                '/tms/default/get-routes-by-client',
                {'client_id':$(this).val()}
            )
                .done(function (result) {

                    $('#tldeliveryproposalbilling-route_from').html('');
                    $.each(result.data_options, function (key, value) {

                        $('#tldeliveryproposalbilling-route_from').append('<option value="' + key + '">' + value + '</option>');
                    });

                    $('#tldeliveryproposalbilling-route_to').html('');
                    $.each(result.data_options, function (key, value) {

                        $('#tldeliveryproposalbilling-route_to').append('<option value="' + key + '">' + value + '</option>');
                    });

                })
                .fail(function () {
                    console.log("server error");
                });
        });

    });

</script>
