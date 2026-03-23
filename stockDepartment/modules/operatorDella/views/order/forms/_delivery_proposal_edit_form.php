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
use common\modules\transportLogistics\models\TlDeliveryProposal;

/* @var $this yii\web\View
/* @var $model common\modules\transportLogistics\models\TlDeliveryProposal */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tl-delivery-proposal-form">
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'route_from',
        [
            'template' => "{label}\n{input-group-begin}{input}{counter}{input-group-end}\n{hint}\n{error}\n",
            'parts' => [
                '{input-group-begin}' => '<div class="input-group">',
                '{input-group-end}' => '</div>',
                '{counter}' => '<div class="input-group-addon" style="background-color: none; border: none; border-radius: none;" >
                            <span class="btn btn-success btn-xs add-route-bt" data-value="' . Url::to(['/operatorDella/route-order/add-store', 'client_id' => $model->client_id]) . '">Добавить+</span>
                            <span class="btn btn-success btn-xs" data-value="' . Url::to(['/operatorDella/route-order/edit-store']) . '" data-id="'.$model->route_from.'" id="edit-sender-store-bt">Изменить</span>
                            </div>',
            ]
        ]
    )->widget(Select2::classname(), [
        'language' => 'ru',
        'data' => \app\modules\operatorDella\models\DeliveryOrderSearch::getPointsByClient($model->client_id),
        'options' => ['placeholder' => 'Выберите адрес отправителя'],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]);
    ?>

    <?= $form->field($model, 'sender_contact_id',
        [
            'template' => "{label}\n{input-group-begin}{input}{counter}{input-group-end}\n{hint}\n{error}\n",
            'parts' => [
                '{input-group-begin}' => '<div class="input-group">',
                '{input-group-end}' => '</div>',
                '{counter}' => '<div class="input-group-addon" style="background-color: none; border: none; border-radius: none;" >
                            <span class="btn btn-success btn-xs add-route-bt" data-value="' . Url::to(['/operatorDella/route-order/add-employee','client_id'=>$model->client_id]) . '">Добавить</span>
                            <span class="btn btn-success btn-xs" data-value="' . Url::to(['/operatorDella/route-order/edit-employee']) . '" data-id="'.$model->sender_contact_id.'" id="edit-sender-contact-bt">Изменить</span>
                            </div>',
            ],
        ]
    )->widget(Select2::classname(), [
        'language' => 'ru',
        'data' => ArrayHelper::map(\common\modules\client\models\ClientEmployees::find()->andWhere(['client_id'=>$model->client_id])->all(),'id','full_name'),
        'options' => ['placeholder' => 'Выберите контакт отправителя'],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]); ?>

    <?= $form->field($model, 'route_to',
        [
            'template' => "{label}\n{input-group-begin}{input}{counter}{input-group-end}\n{hint}\n{error}\n",
            'parts' => [
                '{input-group-begin}' => '<div class="input-group">',
                '{input-group-end}' => '</div>',
                '{counter}' => '<div class="input-group-addon" style="background-color: none; border: none; border-radius: none;" >
                                <span class="btn btn-success btn-xs add-route-bt" data-value="' . Url::to(['/operatorDella/route-order/add-store','client_id'=>$model->client_id]) . '">Добавить+</span>
                                <span class="btn btn-success btn-xs" data-value="' . Url::to(['/operatorDella/route-order/edit-store']) . '" data-id="'.$model->route_to.'" id="edit-recipient-store-bt">Изменить</span>
                                </div>',
            ]
        ]
    )->widget(Select2::classname(), [
        'language' => 'ru',
        'data' => \app\modules\operatorDella\models\DeliveryOrderSearch::getPointsByClient($model->client_id),
        'options' => ['placeholder' => 'Выберите адрес получателя'],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]); ?>

    <?= $form->field($model, 'recipient_contact_id',
        [
            'template' => "{label}\n{input-group-begin}{input}{counter}{input-group-end}\n{hint}\n{error}\n",
            'parts' => [
                '{input-group-begin}' => '<div class="input-group">',
                '{input-group-end}' => '</div>',
                '{counter}' => '<div class="input-group-addon" style="background-color: none; border: none; border-radius: none;" >
                            <span class="btn btn-success btn-xs add-route-bt" data-value="' . Url::to(['/operatorDella/route-order/add-employee','client_id'=>$model->client_id]) . '">Добавить</span>
                            <span class="btn btn-success btn-xs" data-value="' . Url::to(['/operatorDella/route-order/edit-employee']) . '" data-id="'.$model->recipient_contact_id.'" id="edit-recipient-contact-bt">Изменить</span>
                            </div>',
            ],
        ]
    )->widget(Select2::classname(), [
        'language' => 'ru',
        'data' => ArrayHelper::map(\common\modules\client\models\ClientEmployees::find()->andWhere(['client_id'=>$model->client_id])->all(),'id','full_name'),
        'options' => ['placeholder' => 'Выберите контакт получателя'],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]); ?>


    <?= $form->field($model, 'delivery_method')->dropDownList(TlDeliveryProposal::getDeliveryMethodArray()); ?>
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

        <?= $form->field($model, 'delivery_date')->widget(DateControl::className(), [
            'type'=>DateControl::FORMAT_DATETIME,
        ]); ?>

    <?php } ?>

    <?= $form->field($model, 'number_places')->textInput() ?>
    <?= $form->field($model, 'number_places_actual')->textInput() ?>
    <?= $form->field($model, 'mc')->textInput(['maxlength' => 26]) ?>
    <?= $form->field($model, 'mc_actual')->textInput() ?>
    <?= $form->field($model, 'kg')->textInput() ?>
    <?= $form->field($model, 'kg_actual')->textInput() ?>



<!--    --><?//= $form->field($model, 'agent_id')->widget(Select2::classname(), [
//        'language' => 'ru',
//        'data' => TlAgents::getAgentsArray(),
//        'options' => ['placeholder' => Yii::t('transportLogistics/forms','Please select the shipping company')],
//        'pluginOptions' => [
//            'allowClear' => true
//        ],
//    ]);
//    ?>

<!--    --><?//= $form->field($model, 'car_id')->dropDownList( ( $model->isNewRecord  ? [] : TlCars::getCarArray() ),['prompt'=>'']); ?>
<!---->
<!--    --><?//= $form->field($model, 'driver_name')->textInput() ?>
<!--    --><?//= $form->field($model, 'driver_phone')->textInput() ?>
<!--    --><?//= $form->field($model, 'driver_auto_number')->textInput() ?>

    <?php if (!$model->isNewRecord) { ?>
        <?= $form->field($model, 'declared_value')->textInput() ?>
        <?= $form->field($model, 'shipment_description')->textArea() ?>
    <?php } ?>

    <?php if (!$model->isNewRecord && $model->showPriceForFormOperator()) { ?>
        <?= $form->field($model, 'price_invoice')->textInput() ?>
        <?= $form->field($model, 'price_invoice_with_vat')->textInput(['maxlength' => 26]) ?>
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
    'header' => '<h2></h2>',
    'id' => 'add-new-rout-modal'
]); ?>
<?= "<div id='modalContent'></div>"; ?>
<?php Modal::end(); ?>

<script type="text/javascript">

    $(function () {
        var b = $('body');

        /*
         *
         * */
        $('.show-client-delivery-order-bt').on('click', function () {
            console.info('.show-client-delivery-order-bt CLICK');
            $('#add-new-rout-modal').
                modal('show')
                .find('#modalContent')
                .load($(this).data('value'));

        });

        /*
         *
         * */
/*        $('.add-route-bt').on('click', function () {
            console.info('.add-route-bt CLICK 1');
            $('#add-new-rout-modal').
                modal('show')
                .find('#modalContent')
                .load($(this).data('value'));

        });*/

        /*
         *
         * */
        b.on('beforeSubmit', '#add-new-client-employee-form', function (e) {
            addClientEmployeeSubmitForm($(this));
            e.preventDefault();
            return false;
        });

        /*
         *
         * */
        function addClientEmployeeSubmitForm($form) {
            $.post(
                $form.attr("action"), // serialize Yii2 form
                $form.serialize()
            ).done(function (result) {
                    $form.parent().html(result.message);
                    $('#add-new-rout-modal').modal('hide');

                    console.info(result.data_options);

                    var fromValue = $('#tldeliveryproposal-sender_contact_id option:selected').val(),
                        toValue = $('#tldeliveryproposal-recipient_contact_id option:selected').val();


                    $('#tldeliveryproposal-sender_contact_id, #tldeliveryproposal-recipient_contact_id').html('');
                    $.each(result.data_options, function (key, value) {
                        console.info(key);
                        console.info(value);

                        $('#tldeliveryproposal-sender_contact_id, #tldeliveryproposal-recipient_contact_id').append('<option value="' + key + '">' + value + '</option>');
                    });

                    $("#tldeliveryproposal-sender_contact_id [value='" + fromValue + "']").attr("selected", "selected");
                    $("#tldeliveryproposal-recipient_contact_id [value='" + toValue + "']").attr("selected", "selected");

                }).fail(function () {
                    console.log("server error");
                    $form.replaceWith('<button class="newType">Fail</button>').fadeOut()
                });

            return false;
        }

        /*
         *
         * */
        function addRouteSubmitForm($form) {

            $.post(
                $form.attr("action"), // serialize Yii2 form
                $form.serialize()
            )
                .done(function (result) {
                    $form.parent().html(result.message);
                    $('#add-new-rout-modal').modal('hide');

                    console.info(result.data_options);

                    var fromValue = $('#tldeliveryproposal-route_from option:selected').val(),
                        toValue = $('#tldeliveryproposal-route_to option:selected').val();


                    $('#tldeliveryproposal-route_from, #tldeliveryproposal-route_to').html('');
                    $.each(result.data_options, function (key, value) {
                        console.info(key);
                        console.info(value);

                        $('#tldeliveryproposal-route_from, #tldeliveryproposal-route_to').append('<option value="' + key + '">' + value + '</option>');
                    });

                    $("#tldeliveryproposal-route_from [value='" + fromValue + "']").attr("selected", "selected");
                    $("#tldeliveryproposal-route_to [value='" + toValue + "']").attr("selected", "selected");

                })
                .fail(function () {
                    console.log("server error");
                    $form.replaceWith('<button class="newType">Fail</button>').fadeOut()
                });

            return false;
        }

        /*
         *
         * */
        b.on('beforeSubmit', '#add-new-route-form', function (e) {
            addRouteSubmitForm($(this));
            e.preventDefault();
            return false;
        });

        /*
         *
         * */
/*        b.on('change', '#edit-sender-store-bt', function () {
            $.post(
                '/city/default/get-city-by-region',
                {'region_id': $(this).val()}
            ).done(function (result) {

                    $('#store-city_id').html('');
                    $.each(result.data_options, function (key, value) {

                        $('#store-city_id').append('<option value="' + key + '">' + value + '</option>');
                    });
                }).fail(function () {
                    console.log("server error");
                });
        });*/

        /*
         *
         * */
/*        b.on('change', '#store-country_id', function () {
            console.info('ffff2');
            $.post(
                '/city/default/get-region-by-country',
                {'country_id': $(this).val()}
            ).done(function (result) {

                    $('#store-region_id').html('');
                    $.each(result.data_options, function (key, value) {

                        $('#store-region_id').append('<option value="' + key + '">' + value + '</option>');
                    });

                    $('#store-region_id :last').attr('selected', 'selected');
                    $('#store-city_id').html('');

                }).fail(function () {
                    console.log("server error");
                });
        });*/

        /*
         *
         * */
        b.on('change', '#senderrecipientform-recipientcontact', function () {
            console.info($(this).val());
            $('#edit-recipient-contact-bt').attr('data-id',$(this).val());
        });

        b.on('change', '#tldeliveryproposal-recipient_contact_id', function () {
            console.info($(this).val());
            $('#edit-recipient-contact-bt').attr('data-id',$(this).val());
        });

        /*
         *
         * */
        $('#edit-recipient-contact-bt').on('click', function () {
            console.info('#edit-recipient-contact-bt CLICK');
            $('#add-new-rout-modal').
                modal('show')
                .find('#modalContent')
                .load($(this).data('value')+"?id="+$('#edit-recipient-contact-bt').attr('data-id'));
        });

        /*
         *
         * */
        b.on('change', '#senderrecipientform-sendercontact', function () {
            console.info($(this).val());
            $('#edit-sender-contact-bt').attr('data-id',$(this).val());
        });

        b.on('change', '#tldeliveryproposal-sender_contact_id', function () {
            console.info($(this).val());
            $('#edit-sender-contact-bt').attr('data-id',$(this).val());
        });

        /*
         *
         * */
        $('#edit-sender-contact-bt').on('click', function () {
            console.info('#edit-sender-contact-bt CLICK');
            $('#add-new-rout-modal').
                modal('show')
                .find('#modalContent')
                .load($(this).data('value')+"?id="+$('#edit-sender-contact-bt').attr('data-id'));
        });


        /*
         *
         * */
        b.on('change', '#senderrecipientform-sender', function () {
            console.info($(this).val());
            $('#edit-sender-store-bt').attr('data-id',$(this).val());
        });

        b.on('change', '#tldeliveryproposal-route_from', function () {
            console.info($(this).val());
            $('#edit-sender-store-bt').attr('data-id',$(this).val());
        });

        /*
         *
         * */
        $('#edit-sender-store-bt').on('click', function () {
            console.info('#edit-sender-store-bt CLICK');
            var id = $('#edit-sender-store-bt').attr('data-id');
            if(id) {
                $('#add-new-rout-modal').
                    modal('show')
                    .find('#modalContent')
                    .load($(this).data('value')+"?id="+id);
            } else {
                alert('Выберите адрес отправителя для редактирования');
            }
        });

        /*
         *
         * */
        b.on('change', '#senderrecipientform-recipient', function () {
            console.info($(this).val());
            $('#edit-recipient-store-bt').attr('data-id',$(this).val());
        });

        b.on('change', '#tldeliveryproposal-route_to', function () {
            console.info($(this).val());
            $('#edit-recipient-store-bt').attr('data-id',$(this).val());
        });

        /*
         *
         * */
        $('#edit-recipient-store-bt').on('click', function () {
            console.info('#edit-recipient-store-bt CLICK');
            var id = $('#edit-recipient-store-bt').attr('data-id');
            if(id) {
                $('#add-new-rout-modal').
                    modal('show')
                    .find('#modalContent')
                    .load($(this).data('value') + "?id=" +id );
            } else {
                alert('Выберите адрес получателя для редактирования');
            }
        });

    });

</script>