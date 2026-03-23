<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\ecommerce\entities\EcommerceOutbound */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Ecommerce Outbounds', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ecommerce-outbound-view">

    <h1><?= Html::encode($this->title) ?></h1>
    <h1>Короб отгрузки: <?=  $outboundBoxBarcode; ?></h1>

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
            'client_id',
            'responsible_delivery_id',
            'order_number',
            'external_order_number',
            'expected_qty',
            'allocated_qty',
            'accepted_qty',
            'place_expected_qty',
            'place_accepted_qty',
            'mc',
            'kg',
            'status',
            'first_name',
            'middle_name',
            'last_name',
            'customer_name',
            'phone_mobile1',
            'phone_mobile2',
            'email:email',
            'country',
            'region',
            'city',
            'zip_code',
            'street',
            'house',
            'building',
            'entrance',
            'flat',
            'intercom',
            'floor',
            'elevator',
            'customer_address',
            'customer_comment:ntext',
            'ttn:ntext',
            'payment_method',
            'payment_status',
            'data_created_on_client',
            'print_picking_list_date',
            'begin_datetime:datetime',
            'end_datetime:datetime',
            'packing_date:datetime',
            'date_left_warehouse:datetime',
            'date_delivered_to_customer',
            'client_CargoCompany',
            'client_Priority',
            'client_ShippingCountryCode',
            'client_ShippingCity',
            'client_PackMessage:ntext',
            'client_GiftWrappingMessage:ntext',
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
<br />
<br />
<?= Html::a('Скачать логи',["/ecommerce/defacto/report/get-logs","id"=>$model->id],['class' => 'btn btn-success']) ?>
<br />
<br />
<?=\yii\grid\GridView::widget([
	'dataProvider' => $logHistory,
	'columns' => [
		'method_name',
		//'response_error_message',
		[
			'attribute'=> 'response_data',
			'format' => 'raw',
			'label' => 'Request/Response',
			'value' => function ($data)  {
				$reqData = unserialize($data->request_data);
				$resData = unserialize($data->response_data);
				if ($data->method_name == "GetCargoLabel" && isset($resData["Data"]->Data)) {
					$resData["Data"]->Data->FileData = substr($resData["Data"]->Data->FileData, 0, 30)."...";
				}
				return "Response:<pre>".print_r($resData,true)."</pre>".
					"Request: <pre>".print_r($reqData,true)."</pre>";
			},
		],
	],
]);
?>
<br />
<br />
<br />
<br />
<p>
    <?= Html::a('Send-accepted-shipments', ['/ecommerce/defacto/ecom-other/send-accepted-shipments', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
</p>
<br />
<p>
    <?= Html::a('Send-shipment-feedback', ['/ecommerce/defacto/ecom-other/send-shipment-feedback', 'id' => $model->id], ['class' => 'btn btn-info']) ?>
</p>
<br />
<p>
    <?= Html::a('Resend-get-cargo-label', ['/ecommerce/defacto/outbound/resend-get-cargo-label', 'orderNumber' => $model->order_number], ['class' => 'btn btn-warning']) ?>
</p>
<br />
<p>
    <?= Html::a('Cancel-shipment', ['/ecommerce/defacto/ecom-other/cancel-shipment', 'id' => $model->id], ['class' => 'btn btn-danger']) ?>
</p>