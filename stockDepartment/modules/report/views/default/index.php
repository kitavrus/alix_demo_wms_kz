<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 19.12.14
 * Time: 10:03
 */

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
<!--        --><?//= Html::a(Yii::t('transportLogistics/buttons', 'Очистить поиск'), ['index'], ['class' => 'btn btn-primary','style'=>'float:right; margin-left:10px; margin-bottom:10px;']) ?>
    </p>
    <?= $this->render('_search', ['model' => $searchModel]); ?>
    <br />
    <br />

    <?= GridView::widget([
        'id' => 'delivery-proposal-grid-view',
        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
        'floatHeader' => true,
//        'rowOptions'=> function ($model, $key, $index, $grid) {
//
//            // S: TODO Create new function
//            $class = '';
//            switch($model->status) {
//                case TlDeliveryProposal::STATUS_ADD_ROUTE_TO_DP:
//                case TlDeliveryProposal::STATUS_ADD_CAR_TO_ROUTE:
//                    $class = 'dp-row-options-status-add-route-to-dp';
//                    break;
//                case TlDeliveryProposal::STATUS_NOT_ADDED_M3:
//                    $class = 'dp-row-options-status-not-added-m3';
//                    break;
//                default:
//                    $class = '';
//                    break;
//            }
//
//            return ['class'=>$class];
//            // E: TODO Create new function
//        },
        'columns' => [
//            ['class' => 'yii\grid\CheckboxColumn'],
            [
                'attribute'=> 'id',
                'format'=> 'html',
                'value' => function ($data) { return Html::tag('a', $data->id, ['href'=>Url::to(['/tms/default/view', 'id' => $data->id]), 'target'=>'_blank']);},

            ],
            [
                'attribute'=> 'orders',
                'value' => function ($data) { return $data->getExtraFieldValueByName('orders');},
            ],
            [
                'class' => DataColumn::className(),
                'attribute' => 'shipped_datetime',
                'format' => 'datetime',
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
                'attribute' => 'client_id',
                'value' => 'client.title',
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
                'value' => function ($data) use ($storeArray) {return isset($storeArray[$data->route_from]) ? $storeArray[$data->route_from] : '-'; },
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
                'value' => function ($data) use ($storeArray) {return isset($storeArray[$data->route_to]) ? $storeArray[$data->route_to] : '-'; },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => $storeArray,
                    'options' => [
                        'placeholder' => Yii::t('transportLogistics/titles', 'Select route'),
                    ],
                ],
            ],

            'mc_actual',
            'kg_actual',
            'number_places_actual',
            [
                'attribute'=> 'price_invoice_with_vat',
                'format'=>'currency',
            ],
            [
                'attribute'=> 'delivery_type',
                'filter'=> $searchModel::getDeliveryTypeArray(),
                'value' => function ($data) {return $data->getDeliveryTypeValue(); },
            ],
            [
                'attribute'=> 'status',
                'filter'=> $searchModel::getStatusArray(),
                'value' => function ($data) { return TlDeliveryProposal::getStatusArray($data->status);},
            ],

            [
                'attribute'=> 'status_invoice',
                'filter'=> $searchModel::getInvoiceStatusArray(),
                'value' => function ($data) {return TlDeliveryProposal::getInvoiceStatusArray($data->status_invoice);},
            ],
            ['class' => 'yii\grid\ActionColumn',
             'template'=>'{view}',
             'urlCreator'=>function($action, $model, $key, $index) {
                     return Url::toRoute(['/tms/default/view','id'=>(int)$key]);
                 }
            ],
        ],
    ]); ?>
</div>
<div>

    <?= Html::tag('span',Yii::t('transportLogistics/buttons','Export to Excel'),['class' => 'btn btn-success','id'=>'delivery-proposal-export-btn']) ?>
    <?= Html::tag('span',Yii::t('transportLogistics/buttons','Export to excel kpi delivered'),['class' => 'btn btn-info','id'=>'delivery-proposal-export-kpi-delivered-btn']) ?>
    <br />
</div>

<script type="text/javascript">
    $(function(){

        $('#delivery-proposal-export-btn').on('click',function() {
            var keys = window.location.href;
            console.info($('#report-search-form').serialize());
            console.info(keys);
            window.location.href = '/report/default/export-to-excel?'+$('#report-search-form').serialize();
        });

        $('#delivery-proposal-export-kpi-delivered-btn').on('click',function() {
            var keys = window.location.href;
            console.info($('#report-search-form').serialize());
            console.info(keys);
            window.location.href = '/report/default/export-to-excel-kpi-delivered?'+$('#report-search-form').serialize();
        });

//        $('#tldeliveryproposalsearch-shipped_datetime').on('apply.daterangepicker', function(ev, picker) {
//            $(this).trigger('change');
//        });
    });
</script>