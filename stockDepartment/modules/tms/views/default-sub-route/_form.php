<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\modules\client\models\Client;
use kartik\select2\Select2;
use common\modules\transportLogistics\components\TLHelper;
use common\modules\transportLogistics\models\TlAgents;
use common\modules\transportLogistics\models\TlCars;

/* @var $this yii\web\View */
/* @var $model common\modules\transportLogistics\models\TlDeliveryProposalDefaultSubRoute */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tl-delivery-proposal-default-sub-route-form">

    <?php $form = ActiveForm::begin(); ?>
    <?//= $form->field($model, 'client_id')->dropDownList(ArrayHelper::map(Client::findAll(['status' => Client::STATUS_ACTIVE]), 'id', 'title'),['prompt' =>Yii::t('transportLogistics/titles', 'Select client')]); ?>

    <?=
    $form->field($model, 'from_point_id')->widget(Select2::classname(), [
        'language' => 'ru',
        'data' => TLHelper::getStockPointArray($model->client_id),
        'options' => ['placeholder' => Yii::t('transportLogistics/titles', 'Select route from')],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]);
    ?>

    <?=
    $form->field($model, 'to_point_id')->widget(Select2::classname(), [
        'language' => 'ru',
        'data' => TLHelper::getStockPointArray($model->client_id),
        'options' => ['placeholder' => Yii::t('transportLogistics/titles', 'Select route from')],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]);
    ?>
    <?= $form->field($model, 'agent_id')->widget(Select2::classname(), [
        'language' => 'ru',
        'data' => TlAgents::getActiveAgentsArray(),
        'options' => ['placeholder' => Yii::t('transportLogistics/forms','Please select the shipping company')],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]);
    ?>
    <?= $form->field($model, 'car_id')->dropDownList( TlCars::getCarsByAgent($model->agent_id) ); ?>
    <?= $form->field($model, 'transport_type')->dropDownList($model::getTransportTypeArray()) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('buttons', 'Create') : Yii::t('buttons', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script type="text/javascript">


    $(function() {

        $('body').on('change','#tldeliveryproposaldefaultsubroute-agent_id',function() {
            $.post(
                '/tms/default/get-cars-by-agent',
                {'agent_id':$(this).val()}
            )
                .done(function (result) {

                    $('#tldeliveryproposaldefaultsubroute-car_id').html('');
                    $.each(result.data_options, function (key, value) {

                        $('#tldeliveryproposaldefaultsubroute-car_id').append('<option value="' + key + '">' + value + '</option>');
                    });

                })
                .fail(function () {
                    console.log("server error");
                });
        });

    });

</script>
