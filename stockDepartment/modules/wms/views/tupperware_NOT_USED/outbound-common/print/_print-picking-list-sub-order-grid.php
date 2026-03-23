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
use common\helpers\iHelper;
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
<?= \yii\grid\GridView::widget([
    'id' => 'grid-view-order-items',
    'dataProvider' => $dataProvider,
    'layout'=>'{items}',
    'pager'=>false,
    'sorter'=>false,
    'rowOptions'=> function ($model, $key, $index, $grid) {
        $class = iHelper::getStockGridColor($model->status);
        return ['class'=>$class];
    },
    'columns' => [
        ['class' => 'yii\grid\CheckboxColumn',
//            'checkboxOptions' => function($model, $key, $index, $column) {

//             return ['value' => $model->id];
//             $options = [];
//                if($model->status == \common\modules\stock\models\Stock::STATUS_OUTBOUND_PRINTED_PICKING_LIST) {
//                    $options['disabled'] = true;
//                }

//             return $options;
//      }
        ],
        'parent_order_number',
        'order_number',
        [
          'attribute'=>  'to_point_id',
            'value'=>function ($model) {
                $storeTitle = '-МАГАЗИН НЕ НАЙДЕН-';
                if($store = Store::findOne(['id'=>$model->to_point_id])) {
                    $storeTitle = Store::getPointTitle($store->id);
                }

                return $storeTitle;
            }
        ],
        'expected_qty',
        [
            'attribute'=>'allocated_qty',
            'contentOptions' => function ($model, $key, $index, $column) {
                return ['id'=>'allocated-qty-cell-'.$model->id];
            }
        ],
        'accepted_qty',
        [
            'attribute'=>'status',
            'value'=> function($model) {
                return $model->getStatusValue();
            }
        ],
//        [
//            'attribute'=>'actions',
//            'label' => Yii::t('outbound/forms','Actions'),
//            'format' => 'raw',
//            'value' => function($model) {
//                $bt = '-';
//                if($model->status == \common\modules\stock\models\Stock::STATUS_OUTBOUND_PREPARED_DATA_FOR_API) {
//
//                    $bt = \yii\helpers\Html::tag('span', Yii::t('outbound/buttons', 'Complete order'), [
//                            'data'=>['url'=>\yii\helpers\Url::toRoute(['complete','id'=>$model->id])],
//                            'class' => 'btn btn-danger outbound-order-complete-bt',
//                    ]);
//                }
//
//                return $bt;
//            },
//        ]


    ],
]); ?>

<h3 class="pull-right">
    <?= Yii::t('outbound/title','Заказы:') ?> <span id="sum-order" class="label label-primary">0</span>
    <?= Yii::t('outbound/title','Зарезервировано:') ?> <span id="sum-reserved" class="label label-info">0</span>
</h3>