<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 04.08.2015
 * Time: 15:27
 */
use yii\helpers\Html;
use yii\helpers\Url;
use common\modules\store\models\Store;
use common\helpers\iHelper;
use common\modules\transportLogistics\models\TlDeliveryProposalOrders;

$this->title = Yii::t('outbound/titles', 'Report: outbound orders');
$this->params['breadcrumbs'][] = $this->title;
use common\modules\stock\models\Stock;
//\yii\helpers\VarDumper::dump($dataProvider, 10, true); die;
?>

<h1><?= Html::encode($this->title) ?></h1>
<?php //echo $this->render('_search', ['model' => $searchModel, 'clientsArray' => $clientsArray]);?>
<p>
    <?= Html::a(Yii::t('outbound/buttons', 'Create Outbound Order' ), ['create'], ['class' => 'btn btn-success']) ?>
</p>
<?= \yii\grid\GridView::widget([
    'id' => 'grid-view-order-items',
    'dataProvider' => $dataProvider,
    //'filterModel' => $searchModel,
    'rowOptions' => function ($model, $key, $index, $grid) {
        $class = isset($model['consignment_cross_dock_id']) ?'color-orange' : '';
        return ['class' => $class];
    },
    'columns' => [
        [
            'attribute' => 'actions',
            'label' => Yii::t('outbound/forms', 'Actions'),
            'format' => 'raw',
            'value' => function ($model) {
                $bt='';
                if(isset($model['consignment_cross_dock_id']) || !Stock::find()->andWhere(['client_id' => $model['client_id'], 'outbound_order_id' => $model['id']])->count()){
                    $order_type = TlDeliveryProposalOrders::ORDER_TYPE_RPT;
                    if(isset($model['consignment_cross_dock_id'])){
                        $order_type = TlDeliveryProposalOrders::ORDER_TYPE_CROSS_DOCK;
                    }
                    if($deliveryOrder = TlDeliveryProposalOrders::find()->andWhere([
                        'client_id' => $model['client_id'],
                        'order_id' => $model['id'],
                        'order_type' => $order_type,
                    ])->one()){
                        $bt = \yii\helpers\Html::a(
                            Yii::t('outbound/buttons', 'Print box label'),
                            Url::toRoute(['/outbound/car-list/print-label-barcode','id' => $model['id'], 'order_type' => $order_type]),
                            [
                                'class' => 'btn btn-primary',
                                'style' => ' margin-left:10px;',
                                'id' => 'outbound-order-print-barcode-bt',
                            ]
                        );
                    }
                }
                return $bt;
            },
        ],
        [
            'attribute' => 'id',
            'format' => 'html',
            'label' => Yii::t('outbound/forms', 'ID'),
            'value' => function ($data) {
                $order_type = TlDeliveryProposalOrders::ORDER_TYPE_RPT;
                if(isset($data['consignment_cross_dock_id'])){
                    $order_type = TlDeliveryProposalOrders::ORDER_TYPE_CROSS_DOCK;
                }
                return Html::tag('a', $data['id'], ['href' => Url::to(['view', 'id' => $data['id'], 'order_type' => $order_type]), 'target' => '_blank']);
            },
        ],
        [

            'label' => Yii::t('outbound/forms', 'Type'),
            'value' => function ($data) {
                $type = Yii::t('outbound/forms', 'RPT');
                if(isset($data['consignment_cross_dock_id'])){
                    $type = Yii::t('outbound/forms', 'Cross Dock');
                }
                return $type;
            },
        ],
        [
            'attribute' => 'client_id',
            'label' => Yii::t('outbound/forms', 'Client ID'),
            'value' => function ($data) use ($clientsArray) {
                if (isset($clientsArray[$data['client_id']])) {
                    return $clientsArray[$data['client_id']];
                }
                return '-';
            },
        ],
        [

            'label' => Yii::t('outbound/forms', 'Parent order number'),
            'value' => function ($data) {
                if (isset($data['party_number'])) {
                    return $data['party_number'];
                }
                if (isset($data['parent_order_number'])) {
                    return $data['parent_order_number'];
                }
                return '-';
            },
        ],
        [
            'attribute' => 'order_number',
            'label' => Yii::t('outbound/forms', 'Order number'),
        ],
        [
            'attribute' => 'to_point_title',
            'label' => Yii::t('outbound/forms', 'To point title'),
            'value' => function ($model) {
                $storeTitle = '-МАГАЗИН НЕ НАЙДЕН-';
                if ($store = Store::findOne(['id' => $model['to_point_id']])) {
                    $storeTitle = Store::getPointTitle($store->id);
                }
                return $storeTitle;
            }
        ],
        [
            'attribute' => 'expected_number_places_qty',
            'label' => Yii::t('inbound/forms', 'Expected Number Places Qty'),
        ],
//        [
//            'attribute' => 'allocated_qty',
//            'label' => Yii::t('outbound/forms', 'Allocate Qty'),
//            'contentOptions' => function ($model, $key, $index, $column) {
//                return ['id' => 'allocated-qty-cell-' . $model['id']];
//            }
//        ],
        [
            'attribute' => 'accepted_number_places_qty',
            'label' => Yii::t('inbound/forms', 'Accepted Number Places Qty'),
        ],

        [
            'attribute' => 'packing_date',
            'label' => Yii::t('outbound/forms', 'Packing date'),
            'format' => 'datetime'
        ],
        [
            'attribute' => 'date_left_warehouse',
            'label' => Yii::t('outbound/forms', 'Date left our warehouse'),
            'format' => 'datetime'
        ],
        [
            'attribute' => 'date_delivered',
            'label' => Yii::t('outbound/forms', 'Date delivered'),
            'format' => 'datetime'
        ],
//        [
//            'attribute' => 'status',
//            'value' => function ($model) {
//                $oo = \common\modules\outbound\models\OutboundOrder::findOne($model['id']);
//                return $model::getStatusArray($model['status']);
//                //return $model->getStatusValue();
//            }
//        ],
//        [
//            'attribute' => 'cargo_status',
//            'value' => function ($model) {
//                //return $model->getCargoStatusValue();
//                return $model::getStatusArray($model['cargo_status']);
//            }
//        ],

    ],
]); ?>

<div>
</div>