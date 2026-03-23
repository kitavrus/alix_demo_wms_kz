<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\modules\transportLogistics\models\TlCars;
use common\modules\transportLogistics\models\TlAgents;
use common\modules\transportLogistics\components\TLHelper;
use app\modules\transportLogistics\transportLogistics;
use kartik\datecontrol\DateControl;
use yii\helpers\Url;

/* @var $this yii\web\View
 * @var $model common\modules\transportLogistics\models\TlDeliveryProposalRouteCars */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tl-delivery-proposal-route-cars-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'route_city_from')->dropDownList(TLHelper::getCityArray()); // ,['disabled'=>true]?>

    <?= $form->field($model, 'route_city_to')->dropDownList(TLHelper::getCityArray()); // ,['disabled'=>true] ?>

    <?= $form->field($model, 'delivery_date')->widget(DateControl::className(), [
        'type'=>DateControl::FORMAT_DATETIME,
    ]); ?>

    <?= $form->field($model, 'agent_id')->dropDownList(TlAgents::getActiveAgentsArray(),['prompt'=>'']); ?>

    <!--    --><?//= $form->field($model, 'car_id')->dropDownList([]); ?>
    <?= $form->field($model, 'car_id')->dropDownList( ( $model->isNewRecord  ? [] : TlCars::getCarArray() )); ?>

    <?= $form->field($model, 'driver_name')->textInput(); ?>

    <?= $form->field($model, 'driver_phone')->textInput(); ?>

    <?= $form->field($model, 'driver_auto_number')->textInput(); ?>

    <?= $form->field($model, 'cash_no')->dropDownList($model->getPaymentMethodArray()); ?>

    <?= $form->field($model, 'price_invoice')->textInput(); ?>

    <?= $form->field($model, 'price_invoice_with_vat')->textInput(['maxlength' => 26]); ?>

    <!--    --><?//= $form->field($model, 'status')->dropDownList($model->getStatusArray()); ?>

    <?= $form->field($model, 'status_invoice')->dropDownList($model->getInvoiceStatusArray()); ?>

    <?= $form->field($model, 'comment')->textarea(['rows' => 6]); ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('buttons', 'Create') : Yii::t('buttons', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
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
