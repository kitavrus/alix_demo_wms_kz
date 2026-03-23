<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\ecommerce\entities\EcommerceTransfer */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Ecommerce Transfers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ecommerce-transfer-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
//            'client_id',
            'client_BatchId',
            'client_ToBusinessUnitId',
            'client_Status',
            'client_LcBarcode',
            'expected_box_qty',
            'status',
            'api_status',
            'expected_qty',
            'allocated_qty',
            'accepted_qty',
            'print_picking_list_date:datetime',
            'begin_datetime:datetime',
            'end_datetime:datetime',
            'packing_date:datetime',
            'date_left_warehouse:datetime',
            'created_user_id',
            'updated_user_id',
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

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

</div>
