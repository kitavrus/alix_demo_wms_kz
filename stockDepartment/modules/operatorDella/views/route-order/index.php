<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 08.02.2016
 * Time: 9:00
 */

use common\modules\transportLogistics\components\TLHelper;
use yii\helpers\Html;
use app\modules\transportLogistics\transportLogistics;
use kartik\widgets\ActiveForm;
use frontendDepartment\modules\tariff\models\DeliveryCalculatorForm;
use common\modules\billing\models\TlDeliveryProposalBilling;
use common\modules\city\models\City;
use kartik\builder\Form;
use kartik\grid\GridView;
use app\modules\operatorDella\models\DeliveryOrderSearch;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Modal;
?>

<div class="tl-delivery-proposal-form">
    <!--    --><?php /*$form = ActiveForm::begin([
        'id' => 'client-search-form',
        'method' => 'GET',
        'options' => [
            'class'=>'form-inline'
        ],
    ]); */ ?>

    <?php
    $form = ActiveForm::begin(['type' => ActiveForm::TYPE_VERTICAL, 'method' => 'GET']);
    echo Form::widget([
        'model' => $model,
        'form' => $form,
        'columns' => 4,
        'attributes' => [
            'cityFrom' => [
                'type' => Form::INPUT_WIDGET,
                'widgetClass' => '\kartik\widgets\Select2',
                'options' => [
                    'data' => DeliveryCalculatorForm::getDefaultRoutesTo(TlDeliveryProposalBilling::TARIFF_TYPE_PERSON_DEFAULT),
                    'options' => ['placeholder' => 'Выберите адрес отправителя'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],

            ],
            'cityTo' => [
                'type' => Form::INPUT_WIDGET,
                'widgetClass' => '\kartik\widgets\Select2',
                'options' => [
                    'data' => DeliveryCalculatorForm::getDefaultRoutesTo(TlDeliveryProposalBilling::TARIFF_TYPE_PERSON_DEFAULT),
                    'options' => ['placeholder' => 'Выберите адрес получателя'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],
            'client_id' => [
                'type' => Form::INPUT_WIDGET,
                'widgetClass' => '\kartik\widgets\Select2',
                'options' => [
                    'data' => $clientArray,
                    'options' => ['placeholder' => 'Выберите адрес получателя'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],
            'm3' => ['type' => Form::INPUT_TEXT, 'options' => []],
            'kg' => ['type' => Form::INPUT_TEXT, 'options' => []],
            'places' => ['type' => Form::INPUT_TEXT, 'options' => []],
            'phone' => [
                'type' => Form::INPUT_WIDGET,
                'widgetClass' => '\yii\widgets\MaskedInput',
//                'columnOptions'=>[
//                    'template' => "{label}\n{input-group-begin}{counter}{input}{input-group-end}\n{hint}\n{error}\n",
//                    'parts' => [
//                        '{input-group-begin}' => '<div class="input-group">',
//                        '{input-group-end}' => '</div>',
//                        '{counter}' => '<div class="input-group-addon" >+7</div>',
//                    ]
//                ],
                'fieldConfig'=>['addon' => ['prepend'=>['content'=>'+7']]],
                'options' => [

                    'mask' => '999-999-99-99',
//                    'data' => DeliveryCalculatorForm::getDefaultRoutesTo(TlDeliveryProposalBilling::TARIFF_TYPE_PERSON_DEFAULT),
//                    'options' => ['placeholder' => 'Выберите адрес получателя'],
//                    'pluginOptions' => [
//                        'allowClear' => true
//                    ],
                ],
            ],
//            'phone' => [
//                'type' => Form::INPUT_TEXT,
//                'options' => []
//            ],
            'fio' => ['type' => Form::INPUT_TEXT, 'options' => []],
            'ttn' => ['type' => Form::INPUT_TEXT, 'options' => []],
//            'places'=>['type'=>Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter password...']],
        ]
    ]);
    ?>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('client/buttons', 'Search'), ['class' => 'btn btn-success']) ?>
        <?= Html::a(Yii::t('client/buttons', 'Clear'), ['index'], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('client/buttons', 'Создать по Делла'), ['quick-make-order'], ['class' => 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<!--<br/>-->
<br/>
<?php if ($deliveryCost != '') { ?>
    <div class="panel panel-danger" id="delivery-result">
        <div class="panel-heading">
            <strong> Стоимость доставки: </strong>
        </div>
        <div class="panel-body text-center">
            <h2><?php echo $deliveryCost ?></h2>
        </div>
    </div>
<?php } ?>
<?php if ($dataProviderClient) { ?>
    <?php if ($dataProviderClient->query->count()) { ?>
        <h2>клиент</h2>
        <?= GridView::widget([
            'dataProvider' => $dataProviderClient,
            'floatHeader' => true,
            'id' => 'client-grid',
            'columns' => [
                [
                    'attribute' => 'full_name',
                    'format' => 'html',
                    'value' => function ($data) {
                        return Html::tag('a', $data->full_name, ['href' => \yii\helpers\Url::to(['/operatorDella/client/view', 'id' => $data->id]), 'target' => '_blank']);
                    },
                ],
                'phone_mobile',
                [
                    'attribute' => 'sender',
                    'format' => 'raw',
                    'label' => 'Отправитель / Получатель',
                    'value' => function ($data) use ($model) {

                        $modelForm = new \app\modules\operatorDella\models\SenderRecipientForm();
                        $modelForm->kg = $model->kg;
                        $modelForm->m3 = $model->m3;
                        $modelForm->places = $model->places;
                        $modelForm->clientId = $data->id;

                        return $this->render('forms/_sender_recipient_form', ['data' => $data, 'modelForm' => $modelForm]);
                    },
                ],
                [
                    'attribute' => 'orders',
                    'format' => 'raw',
                    'value' => function ($data) {
//                    return Html::tag('a', "Заявки клиента (pop-up)", ['href' => \yii\helpers\Url::to(['/operatorDella/client/view', 'id' => $data->id]), 'target' => '_blank', 'class' => 'btn btn-warning']);
                        return Html::tag('span',
                            "Заявки клиента (pop-up)",
                            [
                                'data' => ['value' => \yii\helpers\Url::toRoute(['show-orders-by-client', 'client-id' => $data->id])],
                                'class' => 'btn btn-warning show-client-delivery-order-bt'
                            ]);
                    },
                ],
            ],
        ]); ?>

    <?php } else { ?>
        <?php echo '<div class="text-center"><span class="btn btn-success btn-lg add-route-bt " data-value="' . \yii\helpers\Url::to(['create-client','phone'=>$model->phone]) . '">ДОБАВИТЬ НОВОГО КЛИЕНТА</span></div>'; ?>
    <?php } ?>
<?php } ?>

<?php //\yii\helpers\VarDumper::dump($dataProvider->getModels(),10,true); ?>

<?php $cityFrom = ''; ?>
<?php if(!empty($model->cityFrom)) { ?>
    <?php $cityFrom = City::findOne($model->cityFrom); ?>
<?php } ?>

<?php $cityTo = ''; ?>
<?php if(!empty($model->cityTo)) { ?>
    <?php $cityTo = City::findOne($model->cityTo); ?>
<?php } ?>

<?php $mc = 0; ?>
<?php $kg = 0; ?>
<?php $places = 0; ?>
<?php if ($dataProviderDeliveryProposal) { ?>
    <?php if ($dataDPs = $dataProviderDeliveryProposal->getModels()) { ?>
        <?php foreach ($dataDPs as $dataDP) { ?>
            <?php $mc += $dataDP->mc; ?>
            <?php $kg += $dataDP->kg; ?>
            <?php $places += $dataDP->number_places; ?>
            <?php //echo $dataDP->id.' / '.$dataDP->mc.' / '.$dataDP->kg.' / '.$dataDP->number_places."<br />"; ?>
            <!--            --><?php //if($dataDPOrders = $dataDP->proposalOrders) { ?>
            <!--                --><?php //foreach($dataDPOrders as $dataDPOrder) { ?>
            <!--                    --><?php //echo $dataDPOrder->order_number; ?>
            <!--                --><?php //} ?>
            <!--            --><?php //} ?>
        <?php } ?>
        <!--        --><?php //echo $mc." m3<br />"; ?>
        <!--        --><?php //echo $kg." kg<br />"; ?>
        <!--        --><?php //echo $places." места<br />"; ?>
    <?php } ?>
<?php } ?>

<?php $cityFromTitle = ''; ?>
<?php if ($cityFrom) { ?>
    <?php $cityFromTitle = $cityFrom->name; ?>
<?php } ?>

<?php $cityToTitle = ''; ?>
<?php if ($cityTo) { ?>
    <?php $cityToTitle = $cityTo->name; ?>
<?php } ?>

<?php if (!empty($mc)) { ?>
    <h2>маршрут</h2>
    <table class="table table-striped table-bordered">
        <tr>
            <td>
                <strong><?php echo $cityFromTitle ?> -></strong>
            </td>
            <td align="center">-></td>
            <td>
                <strong><?php echo $cityToTitle ?> -></strong>
            </td>
        </tr>
        <tr>
            <td>
                погрузка <br/>
                <?php echo $mc . " m3"; ?><br/>
                <?php echo $kg . " kg"; ?><br/>
                <?php echo $places . " места"; ?><br/>
            </td>
            <td align="center"><?php echo $deliveryTerm; ?></td>
            <td>
                разгрузка <br/>
                <?php echo $mc . " m3"; ?><br/>
                <?php echo $kg . " kg"; ?><br/>
                <?php echo $places . " места"; ?><br/>
            </td>
        </tr>
    </table>
<?php } ?>

<?php if ($dataProviderDeliveryProposalTTN) { ?>
    <?php if ($dataProviderDeliveryProposalTTN->query->count()) { ?>
        <?= GridView::widget([
            'dataProvider' => $dataProviderDeliveryProposalTTN,
            //'filterModel' => $searchModel,
            //'filter' => false,
            'floatHeader' => true,
            'id' => 'order-grid',
            'columns' => [
                [
                    'label' => Yii::t('client/forms', 'TTN number'),
                    'value' => function($data){
                        return $data->id;
                    }
                ],
                [
                    'label' => Yii::t('client/forms', 'Customer name'),
                    'value' => function($data) use ($clientArray){
                        return \yii\helpers\ArrayHelper::getValue($clientArray,$data->client_id);
                    }
                ],
                [
                    'label' => Yii::t('client/forms', 'Route from'),
                    'value' => function($data) use ($storeArray) {
                        return \yii\helpers\ArrayHelper::getValue($storeArray,$data->route_from);
                    }
                ],
                [
                    'label' => Yii::t('client/forms', 'Route to'),
                    'value' => function($data) use ($storeArray) {
                        return \yii\helpers\ArrayHelper::getValue($storeArray,$data->route_to);
                    }
                ],
                'created_at:datetime',
                [
                    'attribute' => 'status',
                    'value' => function($data){
                        return $data->getStatusValue();
                    },
                ],
                [
                    'label' => Yii::t('client/forms', 'Price'),
                    'value' => function($data){
                        return $data->showPriceForOperator();
                    }
                ],

                ['class' => 'yii\grid\ActionColumn',
                    'template'=>'{view} {edit} {delete}',
                    'buttons'=>[
                        'delete'=> function ($url, $model, $key) {
                            if($model->status == $model::STATUS_NEW) {
                                return Html::a(Yii::t('client/buttons', 'Delete'), ['/operatorDella/order/delete', 'id'=>$model->id], [
                                    'class' => 'btn-xs btn-danger',
                                    'data' => [
                                        'confirm' => Yii::t('titles', 'Are you sure you want to delete this item?'),
                                        'method' => 'post',
                                    ],
                                ]);
                            }
                            return  '';
                        },

                        'edit'=> function ($url, $model, $key) {
                            if($model->status == $model::STATUS_NEW){
                                return   Html::a(Yii::t('client/buttons', 'Edit'), ['/operatorDella/order/edit-order', 'id'=>$model->id], [
                                    'class' => 'btn-xs btn-warning',
                                ]);
                            }
                            return  '';
                        },

                        'view'=> function ($url, $model, $key) {
                            return   Html::a(Yii::t('client/buttons', 'View'), ['/operatorDella/order/view', 'id'=>$model->id], [
                                'class' => 'btn-xs btn-primary',
                            ]);
                        },

                    ]
                ],
            ],
        ]); ?>
    <?php } ?>
<?php }?>

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
        $('.add-route-bt').on('click', function () {
            console.info('.add-route-bt CLICK 2');
            $('#add-new-rout-modal').
                modal('show')
                .find('#modalContent')
                .load($(this).data('value'));

        });

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

                    var fromValue = $('#senderrecipientform-sendercontact option:selected').val(),
                        toValue = $('#senderrecipientform-recipientcontact option:selected').val();


                    $('#senderrecipientform-sendercontact, #senderrecipientform-recipientcontact').html('');
                    $.each(result.data_options, function (key, value) {
                        console.info(key);
                        console.info(value);

                        $('#senderrecipientform-sendercontact, #senderrecipientform-recipientcontact').append('<option value="' + key + '">' + value + '</option>');
                    });

                    $("#senderrecipientform-sendercontact [value='" + fromValue + "']").attr("selected", "selected");
                    $("#senderrecipientform-recipientcontact [value='" + toValue + "']").attr("selected", "selected");

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

                    var fromValue = $('#senderrecipientform-sender option:selected').val(),
                        toValue = $('#senderrecipientform-recipient option:selected').val();


                    $('#senderrecipientform-sender, #senderrecipientform-recipient').html('');
                    $.each(result.data_options, function (key, value) {
                        console.info(key);
                        console.info(value);

                        $('#senderrecipientform-sender, #senderrecipientform-recipient').append('<option value="' + key + '">' + value + '</option>');
                    });

                    $("#senderrecipientform-sender [value='" + fromValue + "']").attr("selected", "selected");
                    $("#senderrecipientform-recipient [value='" + toValue + "']").attr("selected", "selected");

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
        b.on('change', '#store-region_id', function () {
            $.post(
                '/operatorDella/route-order/get-city-by-region',
                {'region_id': $(this).val()}
            ).done(function (result) {

                    $('#store-city_id').html('');
                    $.each(result.data_options, function (key, value) {

                        $('#store-city_id').append('<option value="' + key + '">' + value + '</option>');
                    });
                }).fail(function () {
                    console.log("server error");
                });
        });

        /*
         *
         * */
        b.on('change', '#store-country_id', function () {

            $.post(
                '/operatorDella/route-order/get-region-by-country',
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
        });

        /*
         *
         * */
        b.on('change', '#senderrecipientform-recipientcontact', function () {
            console.info($(this).val());
            $('#edit-recipient-contact-bt').attr('data-id',$(this).val());
        });

        /*
         *
         * */
        $('#edit-recipient-contact-bt').on('click', function () {
            console.info('#edit-recipient-contact-bt CLICK');
            var id = $('#edit-recipient-contact-bt').attr('data-id');
            if(id) {
                $('#add-new-rout-modal').
                    modal('show')
                    .find('#modalContent')
                    .load($(this).data('value') + "?id=" + id);
            } else {
                alert('Выберите контакт получателя для редактирования');
            }
        });

        /*
         *
         * */
        b.on('change', '#senderrecipientform-sendercontact', function () {
            console.info($(this).val());
            $('#edit-sender-contact-bt').attr('data-id',$(this).val());
        });

        /*
         *
         * */
        $('#edit-sender-contact-bt').on('click', function () {
            console.info('#edit-sender-contact-bt CLICK');
            var id = $('#edit-sender-contact-bt').attr('data-id');
            if(id) {
                $('#add-new-rout-modal').
                    modal('show')
                    .find('#modalContent')
                    .load($(this).data('value') + "?id=" + id);
            }else {
                alert('Выберите контакт отправителя для редактирования');
            }
        });


        /*
         *
         * */
        b.on('change', '#senderrecipientform-sender', function () {
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