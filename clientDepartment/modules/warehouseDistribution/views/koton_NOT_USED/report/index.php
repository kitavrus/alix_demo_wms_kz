<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 19.12.14
 * Time: 10:03
 */

use yii\helpers\Url;
use yii\bootstrap\Modal;
use kartik\datecontrol\DateControl;
use yii\helpers\Html;
use common\modules\transportLogistics\models\TlDeliveryProposal;
use common\modules\client\models\Client;
use common\modules\store\models\Store;
use app\modules\transportLogistics\transportLogistics;
use kartik\grid\GridView;
use kartik\grid\EditableColumn;
use kartik\grid\DataColumn;
use common\modules\transportLogistics\components\TLHelper;
use common\helpers\iHelper;
use common\modules\stock\models\Stock;
use common\modules\outbound\models\OutboundOrder;


/* @var $this yii\web\View
 * @var $searchModel stockDepartment\modules\transportLogistics\models\TlDeliveryProposalSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('outbound/titles', 'Report: outbound orders');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="report-order-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_search', ['model' => $searchModel]); ?>
    <br />
    <br />

    <?= GridView::widget([
        'id' => 'report-order-grid-view',
        'dataProvider' => $dataProvider,
        'floatHeader' => true,
        'rowOptions'=> function ($model, $key, $index, $grid) {
            $class = $model->getClientGridColor();
            return ['class'=>$class];
        },
        'columns' => [
            [
                'attribute'=> 'id',
                'format'=> 'html',
                'value' => function ($data) { return Html::tag('a', $data->id, ['href'=>Url::to(['view', 'id' => $data->id]), 'target'=>'_blank']);},


            ],
           'parent_order_number',
           'order_number',
            [
                'attribute' => 'to_point_id',
                'value' => function($data) use ($clientStoreArray){
                    if($data->to_point_id){

                        return isset ($clientStoreArray[$data->to_point_id]) ? $clientStoreArray[$data->to_point_id] : '-';
                    }
                    return '-';
                }
            ],
            'mc',
//            'kg',
            'accepted_number_places_qty',
            'expected_qty',
            'allocated_qty',
            'accepted_qty',
            [
                'attribute' => 'data_created_on_client',
                'value' => function($data){
                    if($data->data_created_on_client){
                        return Yii::$app->formatter->asDatetime($data->data_created_on_client);
                    }
                    return '-';
                }
            ],
            [
                'attribute' => 'created_at',
                'value' => function($data){
                    if($data->created_at){
                        return Yii::$app->formatter->asDatetime($data->created_at);
                    }
                    return '-';
                }
            ],
            [
                'attribute' => 'packing_date',
                'value' => function($data){
                    if($data->packing_date){
                        return Yii::$app->formatter->asDatetime($data->packing_date);
                    }
                    return '-';
                }
            ],
            [
                'attribute' => 'date_left_warehouse',
                'value' => function($data){
                    if($data->date_left_warehouse){
                        return Yii::$app->formatter->asDatetime($data->date_left_warehouse);
                    }
                    return '-';
                }
            ],
            [
                'attribute' => 'date_delivered',
                'value' => function($data){
                    if($data->date_delivered){
                        return Yii::$app->formatter->asDatetime($data->date_delivered);
                    }
                    return '-';
                }
            ],
            [
                'attribute' => 'cargo_status',
                'value' => function($data){
                    return $data->getCargoStatusValue();
                }
            ],
            [
                'label' => 'WMS',
                'value' => function($data){
                    return $data->calculateWMS();
                }
            ],
            [
                'label' => 'TR',
                'value' => function($data){
                    return $data->calculateTR();
                }
            ],
            [
                'label' => 'Full',
                'value' => function($data){
                    return $data->calculateFULL();
                }
            ],
            [
                'attribute'=>'actions',
                'label' => Yii::t('outbound/forms','Actions'),
                'format' => 'raw',
                'value' => function($model) {
                    if($model->cargo_status == OutboundOrder::CARGO_STATUS_DELIVERED || $model->cargo_status == OutboundOrder::CARGO_STATUS_ON_ROUTE){
                        $bt = \yii\helpers\Html::tag('span', Yii::t('transportLogistics/buttons','Export Outbound'),
                            [
                                'class' => 'btn btn-primary outbound-print-bt',
                                'style' => ' margin-left:10px;',
                                'id' => 'outbound-print-bt',
                                'data-url-value'=>Url::toRoute(['export-rn?id='.$model->id])
                            ]);
                    } else {
                        $bt = \yii\helpers\Html::tag('span', Yii::t('transportLogistics/buttons','Export Outbound'),
                            [
                                'class' => 'btn btn-primary disabled',
                                'style' => ' margin-left:10px;',
                                'id' => 'outbound-print-bt',
                                'data-url-value'=>Url::toRoute(['export-rn?id='.$model->id])
                            ]);
                    }


                    return $bt;
                },
                'visible' => \clientDepartment\modules\client\components\ClientManager::canExportRn(),
            ]
//            [
//                'attribute' => 'from_point_id',
//                'value' => function($data){
//                    if($from = $data->fromPoint){
//                        return $from->title;
//                    }
//                    return '-';
//                }
//            ],
//            [
//                'attribute' => 'to_point_id',
//                'value' => function($data){
//                    if($to = $data->toPoint){
//                        return $to->title;
//                    }
//                    return '-';
//                }
//            ],



//            ['class' => 'yii\grid\ActionColumn',
//             'template'=>'{view}',
//             'urlCreator'=>function($action, $model, $key, $index) {
//                     return Url::toRoute(['/transportLogistics/tl-delivery-proposal/view','id'=>(int)$key]);
//                 }
//            ],
        ],
    ]); ?>
</div>
<div>

    <?= Html::tag('span',Yii::t('transportLogistics/buttons','Export to Exel'),['class' => 'btn btn-success','id'=>'report-order-export-btn', 'data-url'=>Url::toRoute('export-to-excel')]) ?>
    <?= Html::tag('span',Yii::t('transportLogistics/buttons','Export Items to Excel'),['class' => 'btn btn-success','id'=>'report-order-export-full-btn', 'data-url'=>Url::toRoute('export-to-excel-plus-product')]) ?>
    <br />
</div>

<script type="text/javascript">
    $(function(){

        $('#report-order-export-btn').on('click',function() {
            window.location.href = $(this).data('url')+'?'+$('#report-search-form').serialize();
        });

        $('#report-outbound-btn').on('click',function() {
            window.location.href = $(this).data('url')+'?'+$('#report-search-form').serialize();
        });

        $('.outbound-print-bt').on('click',function() {
            window.location.href = $(this).data('url-value');
        });

        $('#report-order-export-full-btn').on('click',function() {
            window.location.href = $(this).data('url')+'?'+$('#outbound-orders-grid-search-form').serialize();
        });

    });
</script>