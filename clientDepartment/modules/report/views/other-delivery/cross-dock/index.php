<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use common\modules\store\models\Store;

/* @var $this yii\web\View */
/* @var $searchModel stockDepartment\modules\inbound\models\InboundOrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('titles', 'Report: cross-dock orders');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cross-dock-order-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel, 'clientsArray' => $clientsArray, 'clientStoreArray' => $clientStoreArray]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'id' => 'cross-dock-order-report',
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
            [
                'attribute'=>  'to_point_id',
                'value'=>function ($model) {
                    $storeTitle = '-МАГАЗИН НЕ НАЙДЕН-';
                    if($store = Store::findOne($model->to_point_id)) {
                        $storeTitle = Store::getPointTitle($store->id);
                    }
                    return $storeTitle;
                }
            ],
            [
                'attribute'=>  'internal_barcode',
                'value'=>function ($model) {
                    return $model->party_number.' / '.ltrim($model->internal_barcode,'2-');
                }
            ],
            'box_m3',
            'expected_number_places_qty',
            'accepted_number_places_qty',
            'date_left_warehouse:datetime',
            'weight_brut',
            'weight_net',
            'created_at:datetime',
            [
                'label' => 'WMS',
                'value' => function($model){
                    return $model->calculateWMS();
                }
            ],
            [
                'label' => 'TR',
                'value' => function($model){
                    return $model->calculateTR();
                }
            ],
            [
                'label' => 'Full',
                'value' => function($model){
                    return $model->calculateFULL();
                }
            ],
        ],
    ]); ?>
</div>
<div>
    <?= Html::tag('span',Yii::t('transportLogistics/buttons','Export to Exсel Full'),['class' => 'btn btn-warning','id'=>'report-order-export-full-btn', 'data-url'=>'export-to-excel-full']) ?>
    <?= Html::tag('span',Yii::t('transportLogistics/buttons','Export to Exсel'),['class' => 'btn btn-success','id'=>'report-order-export-btn', 'data-url'=>'export-to-excel']) ?>
    <br />
</div>

<script type="text/javascript">
    $(function() {
        $('#report-order-export-full-btn').on('click',function() {
            window.location.href = $(this).data('url')+'?'+$('#cross-dock-orders-grid-search-form').serialize();
        });

        $('#report-order-export-btn').on('click',function() {
            window.location.href = $(this).data('url')+'?'+$('#cross-dock-orders-grid-search-form').serialize();
        });
    });
</script>
