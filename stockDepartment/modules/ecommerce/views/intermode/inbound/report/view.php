<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\ecommerce\intermode\inbound\entities\EcommerceInbound */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Ecommerce Inbounds', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ecommerce-inbound-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'client_id',
            'party_number',
            'order_number',
            'expected_box_qty',
            'accepted_box_qty',
            'expected_lot_qty',
            'accepted_lot_qty',
            'expected_product_qty',
            'accepted_product_qty',
            'status',
            'begin_datetime:datetime',
            'end_datetime:datetime',
            'date_confirm',
            'created_user_id',
            'updated_user_id',
            'created_at',
            'updated_at',
            'deleted',
        ],
    ]) ?>
</div>
<?= \yii\grid\GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        'product_barcode',
        'product_expected_qty',
        'product_accepted_qty',
//        'client_box_barcode',
//        'client_lot_sku',
//        'client_product_sku',
//        'our_box_barcode',
    ],
]); ?>