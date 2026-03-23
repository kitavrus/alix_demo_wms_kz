<?php

use yii\helpers\Html;
use kartik\grid\DataColumn;
use kartik\grid\GridView;
use common\modules\transportLogistics\components\TLHelper;
use common\modules\transportLogistics\models\TlAgents;
use common\modules\transportLogistics\models\TlCars;
use kartik\grid\EditableColumn;
use app\modules\transportLogistics\transportLogistics;
use yii\helpers\Url;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $searchModel stockDepartment\modules\transportLogistics\models\TlDeliveryProposalRouteCarsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('transportLogistics/titles', 'Tl Delivery Proposal Car search');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tl-delivery-proposal-route-cars-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a(Yii::t('titles', 'Clear search'), ['index'], ['class' => 'btn btn-primary','style'=>'float:right; margin-left:10px;margin-top: -50px;']) ?>
    </p>

    <?= GridView::widget([
        'id' => 'tl-delivery-proposal-route-cars',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'floatHeader' => true,
        'afterRow' => function ($model, $key, $index, $grid) {
            return $this->render('_after-row-search-by-driver-auto-number',['model'=>$model,'key'=>$key, 'index'=>$index, 'grid'=>$grid]);
        },
        'columns' => [
            'id',
            [
                'class' => DataColumn::className(),
                'attribute' => 'route_city_from',
                'value' => 'routeCityFrom.name',
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TLHelper::getCityArray(),
                    'options' => [
                        'placeholder' => Yii::t('forms', 'Select city'),
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],
            [
                'class' => DataColumn::className(),
                'attribute' => 'route_city_to',
                'value' => 'routeCityTo.name',
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TLHelper::getCityArray(),
                    'options' => [
                        'placeholder' => Yii::t('forms', 'Select city'),
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],
            [
                'class' => DataColumn::className(),
                'attribute' => 'shipped_datetime',
                'value' => function($date){
                    return !empty($date->shipped_datetime) ? Yii::$app->formatter->asDatetime($date->shipped_datetime) : '-';
                },
                'filterType' => GridView::FILTER_DATE_RANGE,
                'filterWidgetOptions' => [
                    'convertFormat'=>true,
                    'pluginOptions'=>[
                        'locale'=>[
                            'separator'=> ' / ',
                            'format'=>'Y-m-d',
                        ]
                    ]
                ],
            ],
            [
                'class' => DataColumn::className(),
                'attribute' => 'agent_id',
                'value' => 'agent.name',
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TlAgents::getActiveAgentsArray(),
                    'options' => [
                        'placeholder' => Yii::t('titles', 'Select agent'),
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
            ],
            [
                'class' => DataColumn::className(),
                'attribute' => 'car_id',
                'value' => 'car.name',
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TlCars::getCarArray(),
                    'options' => [
                        'placeholder' => Yii::t('titles', 'Select car'),
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],

            ],
            [
                'attribute'=> 'cash_no',
                'value' => function ($data) {return $data->getPaymentMethodValue();},
                'filter'=> $searchModel->getPaymentMethodArray(),
            ],
            'price_invoice',
            [
                'class' => EditableColumn::className(),
                'editableOptions' => [
                    'inputType' =>'dropDownList',
                    'data' =>$searchModel::getStatusArray(),
                    'formOptions'=>[
                        'action'=>'edit-by-field'
                    ],
                ],
                'attribute'=> 'status',
                'filter'=> $searchModel::getStatusArray(),
                'value' => function ($data) {return $data::getStatusArray($data->status);},

            ],
            [
                'attribute'=> 'status_invoice',
                'value' => function ($data) {return $data->getInvoiceStatusValue();},
                'filter'=> $searchModel->getInvoiceStatusArray(),
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template'=>'{view} {update} {delete}',
                'buttons'=>[
                    'print-ttn'=>function($url, $model, $key){
                        return Html::a('', $url,['class'=>'glyphicon glyphicon-print']);
                    }
                ]
            ],
        ],
    ]); ?>

</div>

<div>
    <?= Html::tag('span','Экспорт Excel',['class' => 'btn btn-success','id'=>'transport-export-btn']) ?>
    <?= Html::tag('span',Yii::t('transportLogistics/buttons','Mass update'),['class' => 'btn btn-info',
        'id'=>'delivery-proposal-index-mass-update-btn',
        'data-value'=> Url::to(['/tms/tl-delivery-proposal-route-cars/mass-update-popup'])
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
        var b = $('body');
        $('#transport-export-btn').on('click',function() {
            var keys = window.location.href;
            window.location.href = '/tms/tl-delivery-proposal-route-cars/export-to-excel?'+$('#tl-delivery-proposal-route-cars-filters').find('.form-control').serialize();
        });

        $('#tldeliveryproposalroutecarssearch-shipped_datetime').on('apply.daterangepicker', function(ev, picker) {
            $(this).trigger('change');
        });

        $('#delivery-proposal-index-mass-update-btn').on('click',function() {
            HideClearPopup();
            $('#delivery-proposal-index-modal'). modal('show').find('#delivery-proposal-index-content').load($(this).data('value'));
            console.info('run click #delivery-proposal-index-mass-update-btn');
        });

        //S: MASS UPDATE FORM
        b.on('beforeSubmit','#mass-update-model-popup-form', function(e) {

            console.info('#mass-update-popup-form on beforeSubmit');

            var $form = $(this),
                action = $form.attr("action"),
                selected =[];
                $.each($('#tl-delivery-proposal-route-cars tr[data-key]'),function(key){
                   selected.push($(this).attr('data-key'));
                });
               var serialize = $form.serializeArray();
                   serialize.push({'name':'ids','value':selected});
                    console.log(serialize);
            $.post(
                action, // serialize Yii2 form
                serialize
                ,function (responseValue) {
                    var showErrorsContent = '';

                    if(responseValue.errors.length > 0) {
                        $.each(responseValue.errors, function (key, value) {

                            showErrorsContent += '<div class="alert-danger alert" ><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button> Номер заявки [ ' + value.id + ' ] : <br />';

                            $.each(value.errors, function (key, value) {
                                showErrorsContent += "" + key + " "+ value +"<br />";
                            });
                            showErrorsContent += '</div>';
                        });

                        ShowPopupErrors(showErrorsContent)
                    } else {
                        HideClearPopup();
                        window.location.reload();
                    }

                },'json').fail(function () {
                    console.log("server error");
                });

            return false;
        });

        b.on('submit','#mass-update-model-popup-form', function(e) {
            console.info('e.preventDefault on submit');
            e.preventDefault();
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
        var data = $('#tl-delivery-proposal-route-cars-filters').find('.form-control').serialize();
    });
</script>