<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 01.04.15
 * Time: 17:45
 */

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;
use yii\helpers\Url;
use common\modules\store\models\Store;
use common\modules\stock\models\Stock;
use common\helpers\iHelper;
?>

<?php
//echo "<br />";
//echo "<br />";
//echo "<br />";
?>
<?php $this->title = Yii::t('outbound/titles', 'Если вы видете корректные данные, то нажмите кнопку "Загрузить в систему"');?>

<!--<h1>--><?//= $this->title ?><!--</h1>-->

<?= \yii\bootstrap\Alert::widget([
    'options' => [
        'id' => 'alert-message-inbound',
        'class' => 'alert-danger',
    ],
    'body' =>
        '<h3>'
        .$this->title
        .'</h3>'
    ,
]);
?>

<?= \yii\helpers\Html::tag('span',
                            Yii::t('outbound/buttons', 'Load outbound order API').'<span id="show-status-message"></span>',
                            [
                                'data' =>[
                                    'url' => \yii\helpers\Url::toRoute('uploaded-order-save-to-db'),
                                    'unique-key' => $unique_key,
                                    'client-id' => $client_id,
                            ],
                            'class' => 'btn btn-danger',
                            'id' => 'upload-outbound-order-load-bt',
                            'style' => 'float-:right;margin-bottom:10px;']) ?>

<?= \yii\helpers\Html::tag('a',
                            Yii::t('outbound/buttons', 'Cancel load'),
                            [
                                'href'=>'/outbound/default/index',
//                                'data' =>[
//                                    'url' => \yii\helpers\Url::toRoute('uploaded-order-save-to-db'),
//                                    'unique-key' => $unique_key,
//                                    'client-id' => $client_id,
//                            ],
                            'class' => 'btn btn-warning',
//                            'id' => 'upload-outbound-order-load-bt',
                            'style' => 'float-:right;margin-bottom:10px;']) ?>

<?= \yii\grid\GridView::widget([
    'id' => 'grid-view-upload-outbound-order-items',
    'dataProvider' => $dataProvider,
    'layout'=>'{items}',
    'pager'=>false,
    'sorter'=>false,
//    'rowOptions'=> function ($model, $key, $index, $grid) {
//        $class = iHelper::getStockGridColor($model->status);
//        return ['class'=>$class];
//    },
    'columns' => [
//        ['class' => 'yii\grid\CheckboxColumn',
//            'checkboxOptions' => function($model, $key, $index, $column) {
//                $options = [];
//                $options['disabled'] = true;
//                if(in_array($model->status, [
//                    Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL,
//                    Stock::STATUS_OUTBOUND_PREPARED_DATA_FOR_API,
//                    Stock::STATUS_OUTBOUND_DELIVERED,
//                    Stock::STATUS_OUTBOUND_ON_ROAD,
//                ])) {
//                    $options['disabled'] = false;
//                }
//                return $options;
//            }
//        ],
        'party_number',
        'order_number',
        [
            'attribute'=>  'to_point_title',
            'value'=>function ($model) {
                $storeTitle = '-МАГАЗИН НЕ НАЙДЕН-';
                if($store = Store::findOne(['shop_code'=>$model->to_point_title])) {
                    $storeTitle = Store::getPointTitle($store->id);
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
//        'accepted_qty',
//        [
//            'attribute'=>'status',
//            'value'=> function($model) {
//                return $model->getStatusValue();
//            }
//        ],
//        [
//            'attribute'=>'actions',
//            'label' => Yii::t('outbound/forms','Actions'),
//            'format' => 'raw',
//            'value' => function($model) {
//                $bt = '-';
//                if($model->status == \common\modules\stock\models\Stock::STATUS_OUTBOUND_PREPARED_DATA_FOR_API) {
//
//                    $bt = \yii\helpers\Html::tag('span', Yii::t('outbound/buttons', 'Complete order'), [
//                        'data'=>['url'=>\yii\helpers\Url::toRoute(['complete','id'=>$model->id])],
//                        'class' => 'btn btn-danger outbound-order-complete-bt',
//                    ]);
//                }
//
//                return $bt;
//            },
//        ]
    ],
]); ?>