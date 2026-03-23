<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 30.01.15
 * Time: 17:43
 */

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;
use yii\helpers\Url;
use common\modules\store\models\Store;
use common\modules\stock\models\Stock;
use common\modules\outbound\models\OutboundOrder;
?>
<?php //\yii\widgets\Pjax::begin([
//        'timeout'=>false,
//        'enablePushState'=>false,
//
//        'options'=>[
//            'id'=>'pjax-grid-view-order-item-container',
//
//        ],
//        'clientOptions'=>[
//            'url'=>Url::toRoute('get-sub-order-grid'),
////            'type'=>'POST',
//        ],
//    ]
//); ?>
<?= $this->render('_outbound-orders-grid-filter', ['model' => $searchModel,'clearRoute'=>['operation-report']]); ?>

<?= \yii\grid\GridView::widget([
    'id' => 'grid-view-orders',
    'dataProvider' => $dataProvider,
    'layout'=>'{items}',
    'pager'=>false,
    'sorter'=>false,
    'rowOptions'=> function ($model, $key, $index, $grid) {
        $class = $model->getClientGridColor();
        return ['class'=>$class];
    },
//    'afterRow' => function ($model, $key, $index, $grid) {
//        return $this->render('_afterRow_operation-report-grid',['model'=>$model]);
//    },
    'columns' => [
        'parent_order_number',
        'order_number',
        [
          'attribute'=>  'to_point_title',
            'value'=>function ($model) {
                $storeTitle = '-МАГАЗИН НЕ НАЙДЕН-';
                if($to = $model->toPoint){
                   $title = $to->getPointTitleByPattern('full');
                    if(empty($to->shopping_center_name_lat)){
                        $title = str_replace('/','',$title);
                    }
                    return $title;
                }
                return $storeTitle;
            }
        ],
        'expected_qty',
//        [
//            'attribute'=>'allocated_qty',
//            'contentOptions' => function ($model, $key, $index, $column) {
//                return ['id'=>'allocated-qty-cell-'.$model->id];
//            }
//        ],
        [
            'attribute'=> 'allocated_qty',
            'format'=>'raw',
            'value'=> function($model) {
                $ariaValueMin = 0;
                $ariaValueMax = intval($model->expected_qty);
                $ariaValueNow = intval($model->allocated_qty);
                $ValueMaxPercentOne = 100;
                if($ariaValueMax > 0){
                    $ValueMaxPercentOne = ($ariaValueMax / 100);
                }

                $percent = 0;
                if($ariaValueNow > 0) {
                    $percent = (($ariaValueMax - $ariaValueNow) / $ValueMaxPercentOne);
                   // $percent = round($percent,0, PHP_ROUND_HALF_UP);
                }

                $difference = 0;
                if($ariaValueNow > 0) {
                    $difference = 100 - $percent;
                }

                return '<div class="progress progress-operation-report">
                            <div class="progress-bar progress-bar-success progress-bar-operation-report" role="progressbar" aria-valuenow="'.$ariaValueNow.'" aria-valuemin="'.$ariaValueMin.'" aria-valuemax="'.$ariaValueMax.'" style="width: '.$difference.'%; color:black;">
                            '.$ariaValueNow.'
                            </div>
                            <div class="progress-bar progress-bar-danger progress-bar-striped progress-bar-operation-report" style="width: '.$percent.'%"></div>
                        </div>';
            },
            'contentOptions' => function ($model, $key, $index, $column) {
                return ['id'=>'allocated-qty-cell-'.$model->id,'class'=>"progress-"];
            }
        ],
        [
            'attribute'=> 'accepted_qty',
            'format'=>'raw',
            'value'=> function($model) {
                $ariaValueMin = 0;
                $ariaValueMax = intval($model->allocated_qty);
                $ariaValueNow = intval($model->accepted_qty);
                $ValueMaxPercentOne =100;
                if($ariaValueMax > 0){
                    $ValueMaxPercentOne = ($ariaValueMax / 100);
                }

                $percent = 0;
                if($ariaValueNow > 0) {
                    $percent = (($ariaValueMax - $ariaValueNow) / $ValueMaxPercentOne);
                    $percent = round($percent,0, PHP_ROUND_HALF_UP);
                }

                $difference = 0;
                if($ariaValueNow > 0) {
                    $difference = 100 - $percent;
                }

                return '<div class="progress progress-operation-report">
                            <div class="progress-bar progress-bar-success progress-bar-operation-report" role="progressbar" aria-valuenow="'.$ariaValueNow.'" aria-valuemin="'.$ariaValueMin.'" aria-valuemax="'.$ariaValueMax.'" style="width: '.$difference.'%; color:black;">
                            '.$ariaValueNow.'
                            </div>
                            <div class="progress-bar progress-bar-danger progress-bar-striped progress-bar-operation-report" style="width: '.$percent.'%"></div>
                        </div>';
            },
            'contentOptions' => function ($model, $key, $index, $column) {
                return ['id'=>'accepted-qty-cell-'.$model->id,'class'=>"progress-"];
            }
        ],
        [
            'attribute'=>'cargo_status',
            'value'=> function($model) {
                return $model->getCargoStatusValue();
            }
        ],
//        'extra_status'
//        [
//            'attribute'=>'actions',
//            'label' => Yii::t('outbound/forms','Actions'),
//            'format' => 'raw',
//            'value' => function($model) {
//                    $bt = \yii\helpers\Html::tag('span', Yii::t('outbound/buttons', 'Download file'),
//                        [
//                            'class' => 'btn btn-primary',
//                            'style' => ' margin-left:10px;',
//                            'id' => 'outbound-print-bt',
//                            'data-url-value'=>Url::to(['/outbound/default/download-outbound-order-for-api?id='.$model->id])
//                        ]);
//
//                return $bt;
//            },
//        ]
    ],
]); ?>

<?php if(!is_null($noReservedDataProvider)) { ?>

<!--<h3 >--><?//= Yii::t('outbound/title','Не зарезервированные')  ?><!--</h3>-->
<div class="row" style="margin: 20px 1px">
    <span  id="no-reserved" class="label label-danger" style="font-size: 24px;"><?= Yii::t('outbound/title',' Не зарезервированные')  ?></span>
    <a href="#no-find"><?= Yii::t('outbound/title','Не найдены')  ?></a>
</div>


<?= \yii\grid\GridView::widget([
    'id' => 'grid-view-order-items',
    'dataProvider' => $noReservedDataProvider,
    'layout'=>"{summary}\n{items}",
    'pager'=>false,
    'sorter'=>false,
    'rowOptions'=> function ($model, $key, $index, $grid) {
        $class = $model->getGridColor();
        return ['class'=>$class];
    },
//    'afterRow' => function ($model, $key, $index, $grid) {
//        return $this->render('_view_dp_orders_extra',['model'=>$model]);
//    },
    'columns' => [
        [
            'attribute'=>'party_number',
            'label'=>Yii::t('outbound/forms', 'Parent order number'),
            'value'=>function($model) {
                return OutboundOrder::findOne($model->outbound_order_id)->parent_order_number;
            }
        ],
        [
            'attribute'=>'order_number',
            'label'=>Yii::t('outbound/forms', 'Order number'),
            'value'=>function($model) {
                return OutboundOrder::findOne($model->outbound_order_id)->order_number;
            }
        ],
        'product_barcode',
        'expected_qty',
        [
            'attribute'=>'allocated_qty',
            'contentOptions' => function ($model, $key, $index, $column) {
                return ['id'=>'allocated-qty-cell-'.$model->id];
            }
        ],
        'accepted_qty',
//        [
//            'attribute'=>'status',
//            'value'=> function($model) {
//                return (new OutboundOrder)->getStatusValue($model->status);
//            }
//        ],
    ],
]); ?>

<?php } ?>

<?php if(!is_null($noFindDataProvider)) { ?>

<div class="row" style="margin: 20px 1px">
    <span id="no-find" class="label label-danger" style="font-size: 24px;"><?= Yii::t('outbound/title','Не найдены')  ?></span>
    <a href="#no-reserved"><?= Yii::t('outbound/title','Не зарезервированные')  ?></a>
</div>

<?= \yii\grid\GridView::widget([
    'id' => 'grid-view-order-items',
    'dataProvider' => $noFindDataProvider,

    'layout'=>"{summary}\n{items}",
    'pager'=>false,
    'sorter'=>false,
    'rowOptions'=> function ($model, $key, $index, $grid) {
        $class = $model->getGridColor();
        return ['class'=>$class];
    },
    'columns' => [
        [
            'attribute'=>'party_number',
            'label'=>Yii::t('outbound/forms', 'Parent order number'),
            'value'=>function($model) {
                return OutboundOrder::findOne($model->outbound_order_id)->parent_order_number;
            }
        ],
        [
            'attribute'=>'order_number',
            'label'=>Yii::t('outbound/forms', 'Order number'),
            'value'=>function($model) {
                return OutboundOrder::findOne($model->outbound_order_id)->order_number;
            }
        ],
        'product_barcode',
        'expected_qty',
        [
            'attribute'=>'allocated_qty',
            'contentOptions' => function ($model, $key, $index, $column) {
                return ['id'=>'allocated-qty-cell-'.$model->id];
            }
        ],
        'accepted_qty',

//        [
//            'attribute'=>'status',
//            'value'=> function($model) {
//                return (new OutboundOrder)->getStatusValue($model->status);
//            }
//        ],
    ],
]); ?>

<?php } ?>

