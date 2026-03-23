<?php

use common\modules\store\models\Store;
use common\overloads\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Вернуть в заказ / Убрать из заказа';
$this->params['breadcrumbs'][] = $this->title;
?>

<h1><?= Html::encode($this->title) ?></h1>
<?= $this->render('_search', ['model' => $searchModel, 'clientsArray' => $clientsArray, 'storeArray' => $storeArray]); ?>

<?= \yii\grid\GridView::widget([
    'id' => 'grid-view-order-items',
    'dataProvider' => $dataProvider,
    'columns' => [
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
            'attribute' => 'box_barcode',
            'label' => Yii::t('outbound/forms', 'Box Barcode'),
            'format' => 'html',
            'value' => function ($data) use ($searchModel) {
                $urlParams = ['view', 'boxBarcode' => $data['box_barcode']];
                if (!empty($searchModel->product_barcode)) {
                    $urlParams['product_barcode'] = $searchModel->product_barcode;
                }
                return Html::a(
                    $data['box_barcode'],
                    $urlParams,
                    ['target' => '_blank']
                );
            },
        ],
        [
            'label' => Yii::t('outbound/forms', 'Order number'),
            'attribute' => 'outboundOrder.order_number',
        ],
        [
            'label' => Yii::t('outbound/forms', 'To point id'),
            'value' => function ($data) use ($storeArray) {
                $storeTitle = '-МАГАЗИН НЕ НАЙДЕН-';
                if (isset($data['outboundOrder']['to_point_id']) && !empty($data['outboundOrder']['to_point_id'])) {
                    if (isset($storeArray[$data['outboundOrder']['to_point_id']])) {
                        $storeTitle = $storeArray[$data['outboundOrder']['to_point_id']];
                    }
                }
                return $storeTitle;
            }
        ],
        [
            'label' => Yii::t('stock/forms', 'Product qty'),
            'attribute' => 'product_qty',
        ],
        [
            'label' => Yii::t('forms', 'Status'),
            'attribute' => 'status',
            'value' => function ($data) use ($statusArray) {
                return isset($statusArray[$data['status']]) ? $statusArray[$data['status']] : '-';
            }
        ],

        'updated_at:datetime'
    ],
]); ?>