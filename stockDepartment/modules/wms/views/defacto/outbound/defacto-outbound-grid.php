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
use common\helpers\iHelper;
use stockDepartment\modules\wms\assets\DeFactoAsset;

DeFactoAsset::register($this);

$this->title = Yii::t('wms/titles', 'Outbound Orders Grid');
?>
<h1><?= $this->title ?></h1>
<?= $this->render('filters/_defacto-outbound-orders-grid-filter', ['model' => $searchModel,'clearRoute'=>['defacto-outbound-grid']]); ?>
<?= \yii\grid\GridView::widget([
    'id' => 'grid-view-order-items',
    'dataProvider' => $dataProvider,
    //'layout'=>'{items}',
    //'pager'=>false,
    //'sorter'=>false,
    'rowOptions'=> function ($model, $key, $index, $grid) {
        $class = iHelper::getStockGridColor($model->status);
        return ['class'=>$class];
    },
    'columns' => [
//        ['class' => 'yii\grid\CheckboxColumn',
//            'checkboxOptions' => function($model, $key, $index, $column) {
//                $options = [];
//                $options['disabled'] = true;
//                if(in_array($model->status, [
//                            Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL,
//                            Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL,
//                            Stock::STATUS_OUTBOUND_PREPARED_DATA_FOR_API,
//                            Stock::STATUS_OUTBOUND_DELIVERED,
//                            Stock::STATUS_OUTBOUND_ON_ROAD,
//                ])) {
//                    $options['disabled'] = false;
//                }
//                return $options;
//             }
//        ],
        'parent_order_number',
        'order_number',

        [
            'attribute'=>'allocated_qty',
            'contentOptions' => function ($model, $key, $index, $column) {
                return ['id'=>'allocated-qty-cell-'.$model->id];
            }
        ],
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
        [
            'attribute'=>'actions',
            'label' => Yii::t('outbound/forms','Actions'),
            'format' => 'raw',
            'value' => function($model) {
//                $bt = '-';

//                if(in_array($model->status, [
//                    Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL,
//                    Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL,
//                    Stock::STATUS_OUTBOUND_PREPARED_DATA_FOR_API,
//                    Stock::STATUS_OUTBOUND_DELIVERED,
//                    Stock::STATUS_OUTBOUND_ON_ROAD,
//                ])) {

//                if($model->status == \common\modules\stock\models\Stock::STATUS_OUTBOUND_PREPARED_DATA_FOR_API) {
//                if($model->extra_status != 'İşleminiz Başarı ile gerçekleştirildi.') {

                return \yii\helpers\Html::tag('span', Yii::t('outbound/buttons', 'Resend by API'), [
                        'data'=>['url'=>\yii\helpers\Url::toRoute(['resend-api','id'=>$model->id])],
                        'class' => 'btn btn-danger outbound-order-resend-api-bt',
                ]);
//                }

//                return $bt;
            },
        ],
        'extra_status'
//        [
//            'attribute'=>'api_message',
//            'label' => Yii::t('outbound/forms','Api Message'),
//            'format' => 'raw',
//            'value' => function($model) {
//                $m = '-';
//
//                if($model->client_id == 2) { // DeFacto
//                    if($model->extra_fields) {
//                        $extra = \yii\helpers\Json::decode($model->extra_fields);
//                        if(isset($extra['RezerveDagitimResult'])) {
//                            $m = $extra['RezerveDagitimResult'];
//                            if(empty($m)){
//                                $m = 'empty';
//                            }
//                        }
//                    }
//                }
//
//                return $m;
//            },
//        ]
    ],
]); ?>



