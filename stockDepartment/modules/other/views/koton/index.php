<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 30.01.15
 * Time: 17:43
 */

use yii\helpers\Html;
use yii\helpers\Url;
use common\modules\store\models\Store;
use common\helpers\iHelper;
$this->title = Yii::t('outbound/titles', 'Report: outbound orders');
$this->params['breadcrumbs'][] = $this->title;
?>

<h1><?= Html::encode($this->title) ?></h1>
<?= $this->render('_search', ['model' => $searchModel, 'clientsArray' => $clientsArray,'clientStoreArray'=>$clientStoreArray]); ?>

<?= \yii\grid\GridView::widget([
    'id' => 'grid-view-order-items',
    'dataProvider' => $outboundDataProvider,
    'rowOptions'=> function ($model, $key, $index, $grid) {
        $class = iHelper::getStockGridColor($model->status);
        return ['class'=>$class];
    },
    'columns' => [
        [
            'attribute'=> 'id',
            'format'=> 'html',
            'value' => function ($data) { return Html::tag('a', $data->id, ['href'=>Url::to(['view', 'id' => $data->id]), 'target'=>'_blank']);},

        ],
        [
            'attribute'=>'client_id',
            'value'=>function($data) use ($clientsArray){
                if(isset($clientsArray[$data->client_id])){
                    return $clientsArray[$data->client_id];
                }
                return '-';
            },
        ],
        'parent_order_number',
        'order_number',
        [
            'attribute'=>  'to_point_title',
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
        'packing_date:datetime',
        'date_left_warehouse:datetime',
        'date_delivered:datetime',
        [
            'attribute'=>'status',
            'value'=> function($model) {
                return $model->getStatusValue();
            }
        ],
//        [
//            'attribute'=>'cargo_status',
//            'value'=> function($model) {
//                return $model->getCargoStatusValue();
//            }
//        ],
    ],
]); ?>

<?php if($stockDataProvider->query->count()) { ?>
    <?= \kartik\grid\GridView::widget([
        'dataProvider' => $stockDataProvider,
        'columns' => [
            [
                'label' => Yii::t('forms', 'Box barcode'),
                'attribute' => 'box_barcode',
            ],
            [
                'label' => Yii::t('stock/forms', 'Product barcode'),
                'attribute' => 'product_barcode',
            ],
            [
                'label' => Yii::t('forms', 'Quantity'),
                'attribute' => 'qty',
            ],
            [
                'label' => Yii::t('forms', 'Status'),
                'attribute' => 'status',
            ],
        ],
    ]); ?>
<?php } ?>
