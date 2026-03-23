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

$this->title = Yii::t('outbound/titles', ' Не NOMADEX Belarus');
$this->params['breadcrumbs'][] = $this->title;
?>

<h1><?= Html::encode($this->title) ?></h1>
<?//= $this->render('_search', ['model' => $searchModel, 'clientsArray' => $clientsArray,'clientStoreArray'=>$clientStoreArray]); ?>

<?= \yii\grid\GridView::widget([
    'id' => 'grid-view-order-items',
    'dataProvider' => $dataProvider,
//    'rowOptions'=> function ($data, $key, $index, $grid) {
//        $class = iHelper::getStockGridColor($data['status']);
//        return ['class'=>$class,'data-key' => $data['id']];
//    },
    'columns' => [
        [
            'class' => 'yii\grid\CheckboxColumn',
            'checkboxOptions' => function ($data, $key, $index, $column) {

//                if(isset($data['internal_barcode'])) {
//                    return ['value' => '['.$data['id'].']'];
//                }
                return ['value' => $data['id'],];
            }
        ],
        [
            'attribute' => 'id',
            'format' => 'html',
            'value' => function ($data) {
                return Html::tag('a', $data['id'], ['href' => Url::to(['view', 'id' => $data['id']]), 'target' => '_blank']);
            },
        ],
        [
            'label' => Yii::t('outbound/forms', 'Номер партии'),
            'value' => function ($data) {
                // outbound
                if (isset($data['parent_order_number'])) {
                    return $data['parent_order_number'] . ' / ' . $data['order_number'];
                }
                // cross-dock
                if (isset($data['party_number'])) {
                    return $data['party_number'] . ' / ' . ltrim($data['internal_barcode'], '2-');
                }

                return '-';
            },
        ],
        [
            'label' => 'магазин',
            'attribute' => 'to_point_title',
            'value' => function ($data) use ($clientStoreArray) {
                return \yii\helpers\ArrayHelper::getValue($clientStoreArray, $data['to_point_id']);
            }
        ],
        [
            'label' => Yii::t('outbound/forms', 'Ожидаемое кол-во'),
            'attribute' => 'expected_qty',
            'format' => 'raw',
            'value' => function ($data) {
                // outbound
                if (isset($data['expected_qty']) && isset($data['allocated_qty'])) {
                    return $data['expected_qty'];
                } else {
                    // cross-dock
                    return $data['expected_number_places_qty'];
                }
            }
        ],
        [
            'label' => Yii::t('outbound/forms', 'Принятое кол-во'),
            'attribute' => 'accepted_qty',
            'format' => 'raw',
            'value' => function ($data) {
                // outbound
                if (isset($data['expected_qty']) && isset($data['allocated_qty'])) {
                    return $data['accepted_qty'];
                } else {
                    // cross-dock
                    return $data['accepted_number_places_qty'];
                }
            }
        ],
        [
            'attribute' => 'allocated_qty',
            'contentOptions' => function ($data, $key, $index, $column) {
                // outbound
                return ['id' => 'allocated-qty-cell-' . $data['id']];
            }
        ],

        [
            'attribute' => 'status',
            'label' => Yii::t('outbound/forms', 'Статус'),
            'value' => function ($data) use ($statusArray) {
                return \yii\helpers\ArrayHelper::getValue($statusArray, $data['status']);
            }
        ],
        [
            'label' => Yii::t('outbound/forms', 'Статус груза'),
            'attribute' => 'cargo_status',
            'value' => function ($data) use ($statusCargoArray) {
                return \yii\helpers\ArrayHelper::getValue($statusCargoArray, $data['cargo_status']);
            }
        ],
        'packing_date:datetime',
        'accepted_datetime:datetime',
        'updated_at:datetime'
    ],
]); ?>

<div>

    <?= Html::tag('span', Yii::t('transportLogistics/buttons', 'Export to Excel'), ['class' => 'btn btn-success', 'id' => 'report-order-export-btn', 'data-url' => '/outbound-minsk/report/export-to-excel']) ?>
    <br/>
</div>

<script type="text/javascript">
    $(function () {

        $('#report-order-export-btn').on('click', function () {

//            var keys = $('.grid-view').yiiGridView('getSelectedRows'),
            var keys = $('#grid-view-order-items').yiiGridView('getSelectedRows'),
                serialize = [];

//            console.log($('#grid-view-order-items').data('key="39"'));


            serialize.push({'name': 'ids', 'value': keys});

            if (keys.length < 1) {
                alert('Нужно выбрать хотябы одно заявку');
            } else {

                window.location.href = '/report/other-delivery/print-belarus?ids=' + keys;
//                $.post('outbound-print', serialize, function (d) {
//
//                });
            }

            console.info(serialize);
        });
    });
</script>


