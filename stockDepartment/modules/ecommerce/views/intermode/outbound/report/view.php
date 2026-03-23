<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\intermode\controllers\ecommerce\outbound\domain\entities\EcommerceOutbound */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Ecommerce Outbounds', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ecommerce-outbound-view">
    <h1><?= Html::encode($this->title) ?></h1>
    <h1>Короб отгрузки: <?=  $outboundBoxBarcode; ?></h1>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'client_id',
            'order_number',
            'expected_qty',
            'allocated_qty',
            'accepted_qty',
            'place_expected_qty',
            'place_accepted_qty',
            'status',
            'print_picking_list_date',
            'begin_datetime:datetime',
            'end_datetime:datetime',
            'packing_date:datetime',
            'date_left_warehouse:datetime',
            'created_user_id',
            'updated_user_id',
            'created_at:datetime',
            'updated_at:datetime',
            'deleted',
        ],
    ]) ?>
</div>
<?= \yii\grid\GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        'product_sku',
        'product_barcode',
        'expected_qty',
        'allocated_qty',
        'accepted_qty',
    ],
]); ?>