<?php

use yii\helpers\Html;
//use yii\widgets\ActiveForm;
use yii\helpers\Url;
use common\modules\transportLogistics\models\TlCars;
use common\modules\transportLogistics\models\TlAgents;
use app\modules\transportLogistics\transportLogistics;
use common\modules\transportLogistics\components\TLHelper;
use kartik\datecontrol\DateControl;

use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;

/* @var $this yii\web\View
 * @var $model common\modules\transportLogistics\models\TlDeliveryProposalRouteCars
 * @var $modelDpRouteCar common\modules\transportLogistics\models\TlDeliveryProposalRouteTransport */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tl-delivery-proposal-route-cars">

    <?php $form = ActiveForm::begin([
            'id' => 'add-new-route-car-form',
            'type' => ActiveForm::TYPE_HORIZONTAL,
            'formConfig' => ['labelSpan' => 2, 'deviceSize' => ActiveForm::SIZE_SMALL],
        ]
    ); ?>
    <?= Html::beginTag('div',['class'=>'bg-danger']) ?>
    <?= Html::tag('h3',Yii::t('transportLogistics/custom', 'Information about loads in the car'));?>
    <?= Html::endTag('div') ?>

    <?= $form->field($modelDpRouteCar, 'number_places')->textInput(); ?>
    <?= $form->field($modelDpRouteCar, 'number_places_actual')->textInput(); ?>

    <?= $form->field($modelDpRouteCar, 'mc')->textInput(); ?>
    <?= $form->field($modelDpRouteCar, 'mc_actual')->textInput(); ?>

    <?= $form->field($modelDpRouteCar, 'kg')->textInput(); ?>
    <?= $form->field($modelDpRouteCar, 'kg_actual')->textInput(); ?>


    <?= Html::beginTag('div',['class'=>'bg-success']) ?>
    <?= Html::tag('h3',Yii::t('transportLogistics/custom', 'Information about car'));?>
    <?= Html::endTag('div') ?>

    <?= $form->field($model, 'route_city_from')->dropDownList(TLHelper::getCityArray(), ['disabled'=>true])?>

    <?= $form->field($model, 'route_city_to')->dropDownList(TLHelper::getCityArray(), ['disabled'=>true])?>

    <?= $form->field($model, 'shipped_datetime')->widget(DateControl::className(), [
        'type'=>DateControl::FORMAT_DATETIME,

    ]); ?>

    <?= $form->field($model, 'accepted_datetime')->widget(DateControl::className(), [
        'type'=>DateControl::FORMAT_DATETIME,

    ]); ?>


    <?= $form->field($model, 'delivery_date')->widget(DateControl::className(), [
        'type'=>DateControl::FORMAT_DATETIME,
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


<!--    --><?//= $form->field($model, 'car_id')->dropDownList( ( $model->isNewRecord  ? [] : TlCars::getCarArray() )); ?>
    <?= $form->field($model, 'car_id')->dropDownList( TlCars::getCarsByAgent($model->agent_id) ); ?>

    <?= $form->field($model, 'driver_name')->textInput(); ?>

    <?= $form->field($model, 'driver_phone')->textInput(); ?>

    <?= $form->field($model, 'driver_auto_number')->textInput(); ?>

    <?= $form->field($model, 'cash_no')->dropDownList($model->getPaymentMethodArray()); ?>

    <?= $form->field($model, 'price_invoice')->textInput(); ?>



    <?php if (!$model->isNewRecord) { ?>

    <?= $form->field($model, 'price_invoice_with_vat')->textInput(['maxlength' => 26]); ?>

    <?= $form->field($model, 'status_invoice')->dropDownList($model->getInvoiceStatusArray()); ?>

    <?= $form->field($model, 'status')->dropDownList($model->getStatusArray()); ?>

    <?php } ?>

    <?= $form->field($model, 'comment')->textarea(['rows' => 6]); ?>


<div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('transportLogistics/buttons', 'Create') : Yii::t('transportLogistics/buttons', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<script type="text/javascript">

    /*
        $("#add-new-route-car-form").on('beforeSubmit', function(e) {

            addCarRouteSubmitForm($(this));
//            alert('#add-new-route-car-form beforeSubmit');
            console.log("#add-new-route-car-form " + ' beforeSubmit');
//            return false;
        }).on('submit', function(e){
            e.preventDefault();
//            alert('#add-new-route-car-form - submit');
            console.log("#add-new-route-car-form " + ' Submit');
//            return false;
        });
        */

    $(function() {

        $('body').on('change','#tldeliveryproposalroutecars-agent_id',function() {
            $.post(
                    '/tms/default/get-cars-by-agent',
                    {'agent_id':$(this).val()}
                )
                .done(function (result) {

                    $('#tldeliveryproposalroutecars-car_id').html('');
                    $.each(result.data_options, function (key, value) {

                        $('#tldeliveryproposalroutecars-car_id').append('<option value="' + key + '">' + value + '</option>');
                    });

                })
                .fail(function () {
                    console.log("server error");
                });
       });

    });

</script>