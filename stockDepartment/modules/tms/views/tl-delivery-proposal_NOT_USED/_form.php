<?php

use common\modules\transportLogistics\components\TLHelper;
use common\modules\transportLogistics\models\TlCars;
use common\modules\transportLogistics\models\TlAgents;
use common\modules\client\models\Client;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use app\modules\transportLogistics\transportLogistics;
use kartik\datecontrol\DateControl;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;

/* @var $this yii\web\View
/* @var $model common\modules\transportLogistics\models\TlDeliveryProposal */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tl-delivery-proposal-form">

    <?php $form = ActiveForm::begin([
        'fieldConfig'=>[
            'class'=>'\common\components\widgets\ActiveField'
        ]
    ]); ?>

    <?= $form->field($model, 'client_id')->dropDownList(Client::getActiveTMSItems(),['prompt' =>Yii::t('transportLogistics/titles', 'Select client')]); ?>

    <?= $form->field($model, 'company_transporter')->dropDownList($model::getCompanyTransporterArray(),['prompt'=>Yii::t('titles','-')]) ?>
    <?= $form->field($model, 'delivery_type')->dropDownList($model::getDeliveryTypeArray(),['prompt'=>Yii::t('titles','-')]) ?>
    <?= $form->field($model, 'delivery_method')->dropDownList($model::getDeliveryMethodArray(),['prompt'=>Yii::t('titles','-')]) ?>

    <?=
    $form->field($model, 'route_from',
        [
            'visible'=>true,
//            'disabled'=>true,
//            'options'=>['disabled'=>true],
            'template' => "{label}\n{input-group-begin}{input}{counter}{input-group-end}\n{hint}\n{error}\n",
            'parts' => [
                '{input-group-begin}' => '<div class="input-group">',
                '{input-group-end}' => '</div>',
                '{counter}' => '<div class="input-group-addon" style="background-color: none; border: none; border-radius: none;" ><span class="btn btn-success btn-xs add-route-bt" data-value="' . Url::to(['/tms/default/add-store-route']) . '">Добавить+</span></div>',
            ]
        ]
    )->widget(Select2::classname(), [
            'language' => 'ru',
            'data' => TLHelper::getStockPointArray(),
            'options' => ['placeholder' => Yii::t('transportLogistics/titles', 'Select route from')],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
    ?>

    <?=
    $form->field($model, 'route_to',
        [
            'template' => "{label}\n{input-group-begin}{input}{counter}{input-group-end}\n{hint}\n{error}\n",
            'parts' => [
                '{input-group-begin}' => '<div class="input-group">',
                '{input-group-end}' => '</div>',
                '{counter}' => '<div class="input-group-addon" style="background-color: none; border: none; border-radius: none;" ><span class="btn btn-success btn-xs add-route-bt" data-value="' . Url::to(['/tms/default/add-store-route']) . '">Добавить</span></div>',
            ],
        ]
    )->widget(Select2::classname(), [
            'language' => 'ru',
            'data' => TLHelper::getStockPointArray(),
            'options' => ['placeholder' => Yii::t('transportLogistics/titles', 'Select route to')],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);

    ?>

    <?= $form->field($model, 'shipped_datetime')->widget(DateControl::className(), [
        'type'=>DateControl::FORMAT_DATETIME,

    ]); ?>

    <?= $form->field($model, 'expected_delivery_date')->widget(DateControl::className(), [
        'type'=>DateControl::FORMAT_DATETIME,

    ]); ?>


    <?php if (!$model->isNewRecord) { ?>
        <?= $form->field($model, 'accepted_datetime')->widget(DateControl::className(), [
                'type'=>DateControl::FORMAT_DATETIME,
        ]); ?>
        <?php if ($model->client_id != 2 || $model->route_to == 4) { ?>
            <?= $form->field($model, 'delivery_date')->widget(DateControl::className(), [
            'type'=>DateControl::FORMAT_DATETIME,
            ]); ?>
        <?php } ?>
    <?php } ?>

    <?= $form->field($model, 'number_places')->textInput() ?>
    <?= $form->field($model, 'number_places_actual')->textInput() ?>
    <?= $form->field($model, 'mc')->textInput(['maxlength' => 26]) ?>
    <?= $form->field($model, 'mc_actual')->textInput() ?>
    <?= $form->field($model, 'kg')->textInput() ?>
    <?= $form->field($model, 'kg_actual')->textInput() ?>
    <?= $form->field($model, 'seal')->textInput() ?>



    <?= $form->field($model, 'agent_id')->widget(Select2::classname(), [
        'language' => 'ru',
        'data' => TlAgents::getActiveAgentsArray(),
        'options' => ['placeholder' => Yii::t('transportLogistics/forms','Please select the shipping company')],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]);
    ?>

    <?= $form->field($model, 'car_id')->dropDownList( ( $model->isNewRecord  ? [] : TlCars::getCarArray() ),['prompt'=>'']); ?>

    <?= $form->field($model, 'driver_name')->textInput() ?>
    <?= $form->field($model, 'driver_phone')->textInput() ?>
    <?= $form->field($model, 'driver_auto_number')->textInput() ?>

    <?php if (!$model->isNewRecord) { ?>
        <?= $form->field($model, 'price_invoice')->textInput() ?>
        <?= $form->field($model, 'price_invoice_with_vat')->textInput(['maxlength' => 26]) ?>
        <?= $form->field($model, 'price_our_profit')->textInput(['maxlength' => 26]) ?>

    <?php } ?>

    <?php if (!$model->isNewRecord) { ?>
        <?= $form->field($model, 'status')->dropDownList($model->getStatusArray()) ?>
    <?php } ?>

    <?php if (!$model->isNewRecord) { ?>
        <?= $form->field($model, 'status_invoice')->dropDownList($model->getInvoiceStatusArray()) ?>
    <?php } ?>

    <?= $form->field($model, 'cash_no')->dropDownList($model->getPaymentMethodArray(),['prompt'=>Yii::t('titles','Пожалуйста укажите способ оплаты')]) ?>

    <?= $form->field($model, 'change_price')->dropDownList($model->getNoChangePriceArray(),['prompt'=>Yii::t('titles','-')]) ?>

    <?= $form->field($model, 'change_mckgnp')->dropDownList($model->getNoChangeMcKgNpArray(),['prompt'=>Yii::t('titles','-')]) ?>

    <?= $form->field($model, 'comment')->textarea(['rows' => 6]) ?>


    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('transportLogistics/buttons', 'Create') : Yii::t('transportLogistics/buttons', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php Modal::begin([
    'header' => '<h2>Добавить новое направление</h2>',
    'id' => 'add-new-rout-modal'
]); ?>
<?= "<div id='modalContent'></div>"; ?>
<?php Modal::end(); ?>

<script type="text/javascript">

    $(function() {

        var b = $('body');

        b.on('change','#tldeliveryproposal-client_id',function() {
            $.post(
                '/tms/default/get-routes-by-client',
                {'client_id':$(this).val()}
            )
                .done(function (result) {

                    $('#tldeliveryproposal-route_from').html('');
                    $.each(result.data_options, function (key, value) {

                        $('#tldeliveryproposal-route_from').append('<option value="' + key + '">' + value + '</option>');
                    });

                    $('#tldeliveryproposal-route_to').html('');
                    $.each(result.data_options, function (key, value) {

                        $('#tldeliveryproposal-route_to').append('<option value="' + key + '">' + value + '</option>');
                    });

                })
                .fail(function () {
                    console.log("server error");
                });

            $.post(
                '/client/client-settings/get-default-value',
                {'client_id':$(this).val()}
            )
                .done(function (result) {

                    $.each(result.data_options, function (key, value) {

                        $('#tldeliveryproposal-'+key).val(value);

                    });

                })
                .fail(function () {
                    console.log("server error");
                });
        });

        b.on('change','#tldeliveryproposal-agent_id',function() {
            $.post(
                '/tms/default/get-cars-by-agent',
                {'agent_id':$(this).val()}
            )
                .done(function (result) {

                    $('#tldeliveryproposal-car_id').html('');
                    $.each(result.data_options, function (key, value) {

                        $('#tldeliveryproposal-car_id').append('<option value="' + key + '">' + value + '</option>');
                    });

                })
                .fail(function () {
                    console.log("server error");
                });
        });

    });

</script>
