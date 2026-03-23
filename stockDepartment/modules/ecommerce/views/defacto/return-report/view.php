<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\ecommerce\entities\EcommerceOutbound */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Ecommerce Return', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ecommerce-outbound-view">

    <h1><?= Html::encode($this->title) ?></h1>

<!--    <p>-->
<!--        --><?//= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
<!--        --><?//= Html::a('Delete', ['delete', 'id' => $model->id], [
//            'class' => 'btn btn-danger',
//            'data' => [
//                'confirm' => 'Are you sure you want to delete this item?',
//                'method' => 'post',
//            ],
//        ]) ?>
<!--    </p>-->

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
			'id',
			[
				'attribute'=> 'status',
				'format'=> 'html',
				'value' => function ($data) {
					return \common\ecommerce\constants\ReturnOutboundStatus::getValue($data->status);
				},
			],
			'order_number',
			'expected_qty',
			'accepted_qty',
			'client_ReferenceNumber',
			'outbound_box',
			'client_ExternalShipmentId',
			'client_ExternalOrderId',
			'client_OrderSource',
			'client_IsRefundable',
			'client_RefundableMessage',
			'customer_name',
			'city',
			'customer_address',
			'created_at:datetime',
			'updated_at:datetime',
        ],
    ]) ?>

</div>
<?= \yii\grid\GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        'client_SkuId',
        'product_barcode',
        'product_barcode1',
        'expected_qty',
        'accepted_qty',
		[
			'attribute'=> 'image',
			'format'=> 'html',
			'value' => function ($data) {
					return Html::tag('img', $data->client_ImageUrl, ['src'=>$data->client_ImageUrl, 'width'=>'200','height'=>'400']);
			},
		],
    ],
]); ?>