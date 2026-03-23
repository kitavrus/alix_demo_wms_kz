<?php

use yii\helpers\Url;
use yii\bootstrap\Modal;
//use stockDepartment\assets\DpAsset;
use kartik\datecontrol\DateControl;
use yii\helpers\Html;
//use yii\grid\GridView;
use common\modules\transportLogistics\models\TlDeliveryProposal;
use common\modules\client\models\Client;
use common\modules\store\models\Store;
use app\modules\transportLogistics\transportLogistics;
use kartik\grid\GridView;
use kartik\grid\EditableColumn;
use kartik\grid\DataColumn;
use common\modules\transportLogistics\components\TLHelper;


/* @var $this yii\web\View
 * @var $searchModel stockDepartment\modules\transportLogistics\models\TlDeliveryProposalSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('transportLogistics/titles', 'Tl Delivery Proposals');
$this->params['breadcrumbs'][] = $this->title;

?>
    <div class="tl-delivery-proposal-index">
        <p>
            <?= Html::a(Yii::t('transportLogistics/buttons', 'Create Tl Delivery Proposal' ), ['create'], ['class' => 'btn btn-success']) ?>
            <?= Html::a(Yii::t('transportLogistics/buttons', 'Очистить поиск'), ['index'], ['class' => 'btn btn-primary']) ?>

            <?php if($searchModel->countByStatus(TlDeliveryProposal::STATUS_NEW) > 0){
               echo Html::a(Yii::t('titles', 'New ({count})', ['count'=>($searchModel->countByStatus(TlDeliveryProposal::STATUS_NEW))]), ['index', 'TlDeliveryProposalSearch[status]'=>TlDeliveryProposal::STATUS_NEW, 'TlDeliveryProposalSearch[created_at]'=>'2015-04-01 / '.$tomorrow], ['class' => 'btn btn-warning','style'=>'float:right; margin-left:10px;']);
            } ?>
<!--            --><?php /*if($rp = $searchModel->countNotReadyToPayment()){
                echo Html::a(Yii::t('titles', 'Not ready to invoicing from 60 day ({count})', ['count'=>$rp]), ['index', 'TlDeliveryProposalSearch[notReadyToPayment]'=>'true'], ['class' => 'btn btn-danger','style'=>'float:right; margin-left:10px;']);
            } */?>
            <?php if($searchModel->countByStatus(TlDeliveryProposal::STATUS_ADD_ROUTE_TO_DP) > 0){
                echo Html::a(Yii::t('titles', 'Add route to proposal ({count})', ['count'=>($searchModel->countByStatus(TlDeliveryProposal::STATUS_ADD_ROUTE_TO_DP))]), ['index', 'TlDeliveryProposalSearch[status]'=>TlDeliveryProposal::STATUS_ADD_ROUTE_TO_DP, 'TlDeliveryProposalSearch[created_at]'=>'2015-04-01 / '.$tomorrow], ['class' => 'btn btn-danger','style'=>'float:right; margin-left:10px;']);
            } ?>
            <?php if($searchModel->countByStatus(TlDeliveryProposal::STATUS_ADD_CAR_TO_ROUTE) > 0){
                echo Html::a(Yii::t('titles', 'Add car to route ({count})', ['count'=>($searchModel->countByStatus(TlDeliveryProposal::STATUS_ADD_CAR_TO_ROUTE))]), ['index', 'TlDeliveryProposalSearch[status]'=>TlDeliveryProposal::STATUS_ADD_CAR_TO_ROUTE, 'TlDeliveryProposalSearch[created_at]'=>'2015-04-01 / '.$tomorrow], ['class' => 'btn btn-danger','style'=>'float:right; margin-left:10px;']);
            } ?>
            <?php if($searchModel->countByStatus(TlDeliveryProposal::STATUS_NOT_ADDED_M3) > 0){
                echo Html::a(Yii::t('titles', 'Not added m3 ({count})', ['count'=>($searchModel->countByStatus(TlDeliveryProposal::STATUS_NOT_ADDED_M3))]), ['index', 'TlDeliveryProposalSearch[status]'=>TlDeliveryProposal::STATUS_NOT_ADDED_M3, 'TlDeliveryProposalSearch[created_at]'=>'2015-04-01 / '.$tomorrow], ['class' => 'btn btn-danger','style'=>'float:right; margin-left:10px;']);
            } ?>
            <?php if($c = $searchModel->countDelivery2Day()){
                echo Html::a(Yii::t('titles', 'Not delivered >2 day ({count})', ['count'=>$c]), ['index', 'TlDeliveryProposalSearch[delivery2day]'=>'true'], ['class' => 'btn btn-danger','style'=>'float:right; margin-left:10px;']);
            } ?>
            <!--        --><?php //if($isCountConfirm = TlDeliveryProposal::getCountIsWaitingConfirm()) { ?>
            <!--            --><?//= Html::a(Yii::t('transportLogistics/buttons', 'Ждут подтверждения').'  '.Html::tag('span',$isCountConfirm,['class'=>'label label-danger']).'', ['index','TlDeliveryProposalSearch[is_client_confirmed]'=>TlDeliveryProposal::IS_CLIENT_CONFIRMED_WAITING], ['class' => 'btn btn-warning','style'=>'float:right;']) ?>
            <!--        --><?php //} ?>
        </p>

        <?= GridView::widget([
            'id' => 'delivery-proposal-grid-view',
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'floatHeader' => true,
            'rowOptions'=> function ($model, $key, $index, $grid) {
                $class = $model->getGridColor();
                return ['class'=>$class];
            },
            'columns' => [
                ['class' => 'yii\grid\CheckboxColumn'],

                [
                    'attribute'=> 'id',
                    'format'=> 'html',
                    'value' => function ($data) { return Html::tag('a', $data->id, ['href'=>Url::to(['view', 'id' => $data->id]), 'target'=>'_blank']);},

                ],
                [
                    'attribute'=> 'orders',
                    'format'=> 'raw',
                    'value' => function ($data) {
                        return str_replace(', ','<br />',$data->getExtraFieldValueByName('orders'));
                    },
                ],
                [
                    'class' => EditableColumn::className(),
                    'editableOptions' => [
                        'inputType' =>'dropDownList',
                        'data' => TlDeliveryProposal::getDeliveryTypeArray(),
                        'formOptions'=>[
                            'action'=>'edit-by-field'
                        ],
                        'placement' => 'left',
                    ],
                    'refreshGrid' => true,
                    'attribute'=> 'delivery_type',
                    'filter'=> $searchModel::getDeliveryTypeArray(),
                    'value' => function ($data) {return $data->getDeliveryTypeValue(); },

                ],
                [
                    'class' => EditableColumn::className(),
                    'editableOptions' => [
                        'inputType' =>'dropDownList',
                        'data' =>TlDeliveryProposal::getStatusArray(),
                        'afterInput'=> function ($form, $widget) {
                            echo $form->field($widget->model, 'delivery_date')->widget(DateControl::className(), [
                                'type'=>DateControl::FORMAT_DATETIME,
                                'options'=>['id'=>'delivery_date'.$widget->model->id,]

                            ]);
                        },
                        'formOptions'=>[
                            'action'=>'status-edit-by-field',
                        ],
                    ],
                    'attribute'=> 'status',
                    'filter'=> $searchModel::getStatusArray(),
                    'value' => function ($data) { return TlDeliveryProposal::getStatusArray($data->status);},
                ],

                [
                    'class' => DataColumn::className(),
                    'attribute' => 'client_id',
                    'format'=> 'html',
                    'value' => function($data) use ($clientArray){
                        if(isset($clientArray[$data->client_id])){
                            return Html::tag('a', $clientArray[$data->client_id], ['href'=>Url::to(['/client/default/view', 'id' => $data->client_id]), 'target'=>'_blank']);
                        }
                        return Yii::t('titles', 'Not set');
                    },
                    'filterType' => GridView::FILTER_SELECT2,
                    'filterWidgetOptions' => [
                        'data' => $clientArray,
                        'options' => [
                            'placeholder' => Yii::t('transportLogistics/forms', 'Select client')
                        ],
                    ],
                ],

                [
                    'class' => DataColumn::className(),
                    'attribute' => 'route_from',
                    'value' => function ($data) use ($storeArray) {return isset($storeArray[$data->route_from]) ? $storeArray[$data->route_from] : '-';},
                    'filterType' => GridView::FILTER_SELECT2,
                    'filterWidgetOptions' => [
                        'data' => $storeArray,
                        'options' => [
                            'placeholder' => Yii::t('transportLogistics/titles', 'Select route'),
                        ],
                    ],
                ],
                [
                    'class' => DataColumn::className(),
                    'attribute' => 'route_to',
                    'value' => function ($data) use ($storeArray) {return isset($storeArray[$data->route_to]) ? $storeArray[$data->route_to] : '-';},
                    'filterType' => GridView::FILTER_SELECT2,
                    'filterWidgetOptions' => [
                        'data' => $storeArray,
//                    'data' => $searchModel::getRouteFromTo(),
                        'options' => [
                            'placeholder' => Yii::t('transportLogistics/titles', 'Select route'),
                        ],
                    ],
                ],
                [
                    'class' => DataColumn::className(),
                    'attribute' => 'city_to',
//                    'value' => function ($data) use ($cityArray) {return isset($cityArray[$data->route_to]) ? $cityArray[$data->route_to] : '-';},
                    'value' => function ($data) { return isset($data->routeTo->city) ? $data->routeTo->city->name : '-';},
                    'filterType' => GridView::FILTER_SELECT2,
                    'filterWidgetOptions' => [
                        'data' => $cityArray,
//                    'data' => $searchModel::getRouteFromTo(),
                        'options' => [
                            'placeholder' => '',
//                            'placeholder' => Yii::t('transportLogistics/titles', 'Select city to'),
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ],
                ],
                [
                    'class' => DataColumn::className(),
                    'attribute' => 'region_to',
//                    'value' => function ($data) use ($cityArray) {return isset($cityArray[$data->route_to]) ? $cityArray[$data->route_to] : '-';},
                    'value' => function ($data) { return isset($data->routeTo->region) ? $data->routeTo->region->name : '-';},
                    'filterType' => GridView::FILTER_SELECT2,
                    'filterWidgetOptions' => [
                        'data' => $regionArray,
//                    'data' => $searchModel::getRouteFromTo(),
                        'options' => [
                            'placeholder' => '',
//                            'placeholder' => Yii::t('transportLogistics/titles', 'Select region to'),
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ],
                ],
                [
                    'class' => DataColumn::className(),
                    'attribute' => 'country_to',
//                    'value' => function ($data) use ($cityArray) {return isset($cityArray[$data->route_to]) ? $cityArray[$data->route_to] : '-';},
                    'value' => function ($data) { return isset($data->routeTo->country) ? $data->routeTo->country->name : '-';},
                    'filterType' => GridView::FILTER_SELECT2,
                    'filterWidgetOptions' => [
                        'data' => $countryArray,
//                    'data' => $searchModel::getRouteFromTo(),
                        'options' => [
                            'placeholder' => '',
//                            'placeholder' => Yii::t('transportLogistics/titles', 'Select country to'),
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ],
                ],
//            [
//                'class' => EditableColumn::className(),
//                'editableOptions' => [
//                    'inputType' =>'\kartik\widgets\DatePicker',
//                    'formOptions'=>[
//                        'action'=>'edit-by-field'
//                    ],
//                    'pluginOptions' => [
//                        'autoclose'=>true,
//                        'format' => 'dd-M-yyyy'
//                    ]
//                ],
//                'attribute'=> 'shipped_datetime',
//                'format'=>'date',
//            ],
                [
                    'class' => DataColumn::className(),
                    'attribute' => 'shipped_datetime',
                    'value' => function($date){
                        return !empty($date->shipped_datetime) ? Yii::$app->formatter->asDatetime($date->shipped_datetime) : '-';
                    },
                    'filterType' => GridView::FILTER_DATE_RANGE,
                    'filterWidgetOptions' => [
                        'convertFormat'=>true,
//                    'hideInput'=>true,
                        'pluginOptions'=>[
                            'locale'=>[
                                'separator'=> ' / ',
                                'format'=>'Y-m-d',
                            ]
                        ]
                    ],
                ],
                [
                    'attribute'=>'mc_kg_np',
                    'label'=>'M3&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;Кг&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;Места&nbsp;&nbsp;&nbsp;',
                    'encodeLabel'=>false,
                    'value'=>function($data) {
                        return $data->mc_actual.' / '.$data->kg_actual.' / '.$data->number_places_actual;
                    },
                ],
//                'mc_actual',
//                'kg_actual',
//                'number_places_actual',
//            [
//                'class' => EditableColumn::className(),
//                'attribute'=> 'cash_no',
//                'editableOptions' => [
//                    'inputType' =>'dropDownList',
//                    'data' =>TlDeliveryProposal::getPaymentMethodArray(),
//                    'formOptions'=>[
//                        'action'=>'edit-by-field'
//                    ],
//                    'displayValue' => TlDeliveryProposal::getPaymentMethodArray($searchModel->cash_no),
//                    'displayValueConfig' => TlDeliveryProposal::getPaymentMethodArray(),
//
//                ],
//
//                'filter'=> $searchModel->getPaymentMethodArray(),
//            ],
//            [   'class' => EditableColumn::className(),
//                'attribute'=> 'price_invoice',
//                'editableOptions' => [
//                    'formOptions'=>[
//                        'action'=>'edit-by-field'
//                    ],
//                ],
//                'format'=>'currency',
//
//            ],
                [   'class' => EditableColumn::className(),
                    'attribute'=> 'price_invoice_with_vat',
                    'editableOptions' => [
                        'formOptions'=>[
                            'action'=>'edit-by-field'
                        ],
                    ],
                    'format'=>'currency',

                ],
//            [   'class' => EditableColumn::className(),
//                'attribute'=> 'price_invoice_with_vat',
//                'editableOptions' => [
//                    'formOptions'=>[
//                         'action'=>'edit-by-field'
//                    ],
//                ],
//                'format'=>'currency',
//
//            ],

                [
                    'class' => EditableColumn::className(),
                    'editableOptions' => [
                        'inputType' =>'dropDownList',
                        'data' => TlDeliveryProposal::getInvoiceStatusArray(),
                        'formOptions'=>[
                            'action'=>'edit-by-field'
                        ],
                        'placement' => 'left',
                    ],
                    'refreshGrid' => true,
                    'attribute'=> 'status_invoice',
                    'filter'=> $searchModel::getInvoiceStatusArray(),
                    'value' => function ($data) {return TlDeliveryProposal::getInvoiceStatusArray($data->status_invoice);},

                ],
                ['class' => 'yii\grid\ActionColumn',
//                    'template'=>'{view} {update} {print-ttn} {delete}',
                    'template'=>'{view} {update}',
                    'buttons'=>[]
                ],
            ],
        ]); ?>
    </div>
    <div>
        <?= Html::tag('span',Yii::t('transportLogistics/buttons','Export to Exel'),['class' => 'btn btn-success','id'=>'delivery-proposal-export-btn']) ?>

        <?= Html::tag('span',Yii::t('transportLogistics/buttons','Add car to first route'),['class' => 'btn btn-warning',
            'id'=>'add-first-car-btn',
            'data-value'=> Url::toRoute(['add-first-route-car-popup'])
        ]) ?>

<!--        --><?php /*echo Html::tag('span',Yii::t('transportLogistics/buttons','Add separate car to first route'),['class' => 'btn btn-danger',
            'id'=>'add-separate-first-car-btn',
            'data-value'=> Url::toRoute(['add-separate-first-route-car-popup'])
        ]) */?>

<!--        --><?//= Html::tag('span',Yii::t('transportLogistics/buttons','Add driver'),['class' => 'btn btn-warning',
//            'id'=>'delivery-proposal-index-add-car-btn',
//            'data-value'=> Url::toRoute(['add-car-popup'])
//        ]) ?>

<!--        --><?//= Html::tag('span',Yii::t('transportLogistics/buttons','Add route'),['class' => 'btn btn-info',
//            'id'=>'delivery-proposal-index-add-route-btn',
//            'data-value'=> Url::toRoute(['add-route-popup'])
//        ]) ?>

        <?= Html::tag('span',Yii::t('transportLogistics/buttons','Mass update'),['class' => 'btn btn-info',
            'id'=>'delivery-proposal-index-mass-update-btn',
            'data-value'=> Url::toRoute(['mass-update-popup'])
        ]) ?>
<!--        --><?php /*echo Html::tag('span',Yii::t('transportLogistics/buttons','Merge'),['class' => 'btn btn-warning',
            'id'=>'delivery-proposal-index-merge-btn',
            'data-url'=> Url::toRoute(['merge-proposal-orders'])
        ]) */?>

        <?= Html::tag('span',Yii::t('transportLogistics/buttons','Move orders to first record'),['class' => 'btn btn-danger',
            'id'=>'index-move-delivery-proposal-orders-to-first-order-btn',
            'data-url'=> Url::toRoute(['move-orders-to-first-order'])
        ]) ?>
    </div>
<?php Modal::begin([
    'header' => '<h4 id="delivery-proposal-index-header"></h4>',
    'id' => 'delivery-proposal-index-modal'
]); ?>
<?= "<div id='delivery-proposal-index-errors'></div>"; ?>
<?= "<div id='delivery-proposal-index-content'></div>"; ?>
<?php Modal::end(); ?>

    <script type="text/javascript">
        $(function(){

            $('#delivery-proposal-export-btn').on('click',function() {
                var keys = window.location.href;
                console.info($('#delivery-proposal-grid-view-filters').find('.form-control').serialize());
                console.info(keys);
                window.location.href = '/tms/default/export-to-excel?'+$('#delivery-proposal-grid-view-filters').find('.form-control').serialize();
            });

            $('#tldeliveryproposalsearch-shipped_datetime').on('apply.daterangepicker', function(ev, picker) {
                $(this).trigger('change');
            });


            //S:
            var b = $('body');
            b.on('change','#carmodelpopup-agent_id',function() {
                $.post(
                    '/tms/default/get-cars-by-agent',
                    {'agent_id':$(this).val()}
                ).done(function (result) {

                        $('#carmodelpopup-car_id').html('');
                        $.each(result.data_options, function (key, value) {

                            $('#carmodelpopup-car_id').append('<option value="' + key + '">' + value + '</option>');
                        });
                    }).fail(function () {
                        console.log("server error");
                    });
            });
//
//
            $('#delivery-proposal-index-add-car-btn').on('click',function() {

                var keys = $('#delivery-proposal-grid-view').yiiGridView('getSelectedRows');

                HideClearPopup();

                if(keys.length < 1) {

                    ShowPopupContent('Нужно выбрать хотябы одно заявку');

                } else {

                    $('#delivery-proposal-index-modal').
                        modal('show')
                        .find('#delivery-proposal-index-content')
                        .load($(this).data('value'));
                }

                console.info('run click #delivery-proposal-index-add-car-btn');
            });

            b.on('click','#add-first-car-btn', function() {

                var keys = $('#delivery-proposal-grid-view').yiiGridView('getSelectedRows');

                HideClearPopup();

                if(keys.length < 1) {

                    ShowPopupContent('Нужно выбрать хотябы одну заявку');

                } else {

                    $('#delivery-proposal-index-modal').
                        modal('show')
                        .find('#delivery-proposal-index-content')
                        .load($(this).data('value'));
                }

            });

/*            b.on('click','#add-separate-first-car-btn', function() {
                var keys = $('#delivery-proposal-grid-view').yiiGridView('getSelectedRows');
                HideClearPopup();
                if(keys.length < 1) {
                    ShowPopupContent('Нужно выбрать хотябы одну заявку');
                } else {
                    $('#delivery-proposal-index-modal').
                        modal('show')
                        .find('#delivery-proposal-index-content')
                        .load($(this).data('value'));
                }
            });*/

            //S: ADD MASS CAR
            b.on('beforeSubmit','#car-model-popup-form', function(e) {

                console.info('#car-model-popup-form on beforeSubmit');

                var $form = $(this),
                    action = $form.attr("action"),
                    keys = $('#delivery-proposal-grid-view').yiiGridView('getSelectedRows'),
                    serialize = $form.serializeArray();
                serialize.push({'name':'ids','value':keys});
                ShowPopupContent('Идет обработка данных, пожалуйста подождите...');
                $.post(
                    action, // serialize Yii2 form
                    serialize
                ).done(function (result) {

                        if(result.message == 'error'){
                            ShowPopupContent(result.error);
                            //$('#delivery-proposal-index-errors').text(result.error);
                        } else {
                            HideClearPopup()
                        }

                    }).fail(function () {
                        console.log("server error");
                    });

                return false;
            });

            b.on('submit','#car-model-popup-form', function(e) {
                console.info('e.preventDefault on submit');
                e.preventDefault();
            });
            //E: ADD MASS CAR


            //S: ADD MASS ROUTE
            $('#delivery-proposal-index-add-route-btn').on('click',function() {
                var keys = $('#delivery-proposal-grid-view').yiiGridView('getSelectedRows'),
                    serialize = [],
                    showContent = '';

                HideClearPopup();
                serialize.push({'name':'ids','value':keys});

                if(keys.length < 1) {
                    ShowPopupContent('Нужно выбрать хотябы одно заявку');
                } else {
                    $.post($(this).data('value'),serialize,function(responseValue) {
//                $.post($(this).data('value'),{'ids':keys},function(responseValue) {

                        console.info(responseValue.errors);
                        console.info(responseValue.errors.length);

                        if(responseValue.errors.length > 0) {
                            $.each(responseValue.errors, function (key, value) {
                                if (value.length) {
                                    showContent += value + "<br />";
                                }
                            });
                            console.info('errors is empty');

                        } else {
                            showContent = responseValue.data;
                        }
                        ShowPopupContent(showContent);
                    },'json');
                }
                console.info('run click #delivery-proposal-index-add-route-btn');
            });

            b.on('beforeSubmit','#route-model-popup-form', function(e) {

                console.info('#route-model-popup-form on beforeSubmit');

                var $form = $(this),
                    action = $form.attr("action"),
                    keys = $('#delivery-proposal-grid-view').yiiGridView('getSelectedRows'),
                    serialize = $form.serializeArray();


                console.info(keys);

                serialize.push({'name':'ids','value':keys});

                $.post(
                    action, // serialize Yii2 form
                    serialize
                ).done(function (result) {
                        HideClearPopup();
                    }).fail(function () {
                        console.log("server error");
                    });

                return false;
            });

            b.on('submit','#route-model-popup-form', function(e) {
                console.info('e.preventDefault on submit');
                e.preventDefault();
            });
            //E: ADD MASS ROUTE

            //S: Mass update
//
            $('#delivery-proposal-index-mass-update-btn').on('click',function() {

                //var keys = $('#delivery-proposal-grid-view').yiiGridView('getSelectedRows');

                HideClearPopup();

//                if(keys.length < 1) {
//                    ShowPopupContent('Нужно выбрать хотябы одно заявку');
//                } else {
                    $('#delivery-proposal-index-modal').
                        modal('show')
                        .find('#delivery-proposal-index-content')
                        .load($(this).data('value'));
                //}

                console.info('run click #delivery-proposal-index-mass-update-btn');
            });

            $('#delivery-proposal-index-merge-btn').on('click',function() {

                    var keys = $('#delivery-proposal-grid-view').yiiGridView('getSelectedRows');

                    if(keys.length < 2) {

                        alert('Нужно выбрать минимум 2 заявки');
                    } else {
                        if(confirm('Вы точно хотите объединить эти записи?')) {
                            ShowPopupContent('Подождите пожалуйста, идет обработка данных');
                            $.post(
                                $(this).data('url'),
                                {'ids': keys},
                                function (d) {

                                }
                            );
                        }
                    }

                console.info('run click #delivery-proposal-index-merge-btn');
            });

            $('#index-move-delivery-proposal-orders-to-first-order-btn').on('click',function() {

                    var keys = $('#delivery-proposal-grid-view').yiiGridView('getSelectedRows');

                    if(keys.length < 2) {
                        alert('Нужно выбрать минимум 2 заявки');
                    } else {
                        if(confirm('Вы точно хотите объединить эти записи?')) {
                            ShowPopupContent('Подождите пожалуйста, идет обработка данных');
                            $.post($(this).data('url'), {'ids': keys}, function (d) {

                            });
                        }
                    }

                console.info('run click #delivery-proposal-index-merge-btn');
            });

            //S: MASS UPDATE FORM
            b.on('beforeSubmit','#mass-update-model-popup-form', function(e) {

                console.info('#mass-update-popup-form on beforeSubmit');

                var $form = $(this),
                    action = $form.attr("action"),
                    submitButton =  $('#mass-submit-button'),
                    keys = $('#delivery-proposal-grid-view').yiiGridView('getSelectedRows'),
                    filterQuery = $('#delivery-proposal-grid-view-filters').find('.form-control').serialize(),
                    buttonText = submitButton.text(),
                    serialize = $form.serializeArray();
                    serialize.push({'name':'ids','value':keys});
                    submitButton.text('Подождите пожалуйста, идет обработка данных...');
                $.post(
                    action +'?'+filterQuery, // serialize Yii2 form
                    serialize
                    ,function (responseValue) {
                        var showErrorsContent = '';

                        if(responseValue.errors.length > 0) {
                            $.each(responseValue.errors, function (key, value) {

                                showErrorsContent += '<div class="alert-danger alert" ><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button> Номер заявки [ ' + value.id + ' ] : <br />';

//                            if(value.errors.length > 0) {
                                $.each(value.errors, function (key, value) {
                                    showErrorsContent += "" + key + " "+ value +"<br />";
                                });
//                            }
                                showErrorsContent += '</div>';
                            });

                            ShowPopupErrors(showErrorsContent)
                        } else {
                            HideClearPopup();
                            window.location.href = '/tms/default/index?'+ filterQuery;
                        }
                        submitButton.text(buttonText);
                    },'json').fail(function () {
                        console.log("server error");
                    });

                return false;
            });

            b.on('submit','#mass-update-model-popup-form', function(e) {
                console.info('e.preventDefault on submit');
                e.preventDefault();
            });

            //E: MASS UPDATE FORM
        });


        /*
         * Set content and show popup
         * @param string Content be should showed
         * */
        function ShowPopupContent(toShow)
        {
            $('#delivery-proposal-index-modal').
                modal('show')
                .find('#delivery-proposal-index-content')
                .html(toShow);
        }

        /*
         * Set errors in popup
         * @param string Content be should showed
         * */
        function ShowPopupErrors(toShow)
        {
            $('#delivery-proposal-index-modal')
                .find('#delivery-proposal-index-errors')
                .html(toShow);
        }

        /*
         * Hide and clear content in popup
         * */
        function HideClearPopup()
        {
            $('#delivery-proposal-index-modal').modal('hide');
            $('#delivery-proposal-index-errors').html('');
            $('#delivery-proposal-index-content').html('');
        }

    </script>

<?php
// TODO  Добавить вывод в заголовке всплывающего окна. Сообщение о том, что мы редактируем. Например: Массовое обновление или Добавление машин
?>