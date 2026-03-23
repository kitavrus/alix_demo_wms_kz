<?php
use yii\helpers\Html;
use yii\helpers\Url;
use common\modules\store\models\Store;
use common\helpers\iHelper;
use common\modules\stock\models\Stock;

$this->title = Yii::t('outbound/titles', 'Report: outbound boxes');
$this->params['breadcrumbs'][] = $this->title;
?>

<h1><?= Html::encode($this->title) ?></h1>
<?= $this->render('_search', ['model' => $searchModel]); ?>

<?= \yii\grid\GridView::widget([
    'id' => 'grid-view-order-items',
    'dataProvider' => $dataProvider,
//    'rowOptions'=> function ($model, $key, $index, $grid) {
//        $class = iHelper::getStockGridColor($model->status);
//        return ['class'=>$class];
//    },
    'columns' => [
//        [
//            'attribute' => 'id',
//            'format' => 'html',
//            'value' => function ($data) {
//                return Html::tag('a', $data['id'], ['href' => Url::to(['view', 'id' => $data['id']]), 'target' => '_blank']);
//            },
//
//        ],

        [
            'label' => Yii::t('stock/forms', 'Box Barcode'),
            'attribute' => 'box_barcode',
        ],
        [
            'label' => Yii::t('outbound/forms', 'Parent order number'),
            'attribute' => 'outboundOrder.parent_order_number',
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
            'label' => Yii::t('stock/forms', 'Box volume'),
            'attribute' => 'box_size_m3',
        ],

        [
            'label' => Yii::t('forms', 'Status'),
            'attribute' => 'status',
            'value' => function ($data) use ($statusArray) {
                return isset ($statusArray[$data['status']]) ? $statusArray[$data['status']] : '-';
            }
        ],
    ],
]); ?>

<div>

    <?= Html::tag('span', Yii::t('transportLogistics/buttons', 'Export to Exel'), ['class' => 'btn btn-success', 'id' => 'report-order-export-btn', 'data-url' => 'export-to-excel']) ?>
    <br/>
</div>

<script type="text/javascript">
    $(function () {

        $('#report-order-export-btn').on('click', function () {
            window.location.href = $(this).data('url') + '?' + $('#outbound-orders-grid-search-form').serialize();
        });

    });
</script>


