<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\ecommerce\intermode\outbound\entities\EcommerceOutbound */

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
		'product_barcode',
		'product_name',
		'product_model',
		'expected_qty',
		'allocated_qty',
		'accepted_qty',
        'product_brand',
        'product_color',
		'product_sku',
    ],
]); ?>


<br />
<br />
<br />
<br />
<table class="table table-striped table-bordered">
	<tr>
		<td>
			<p>
				<?= Html::a('Это дубль', ['/intermode/ecommerce/default/reset-and-set-repeat-outbound', 'id' => $model->id], ['class' => 'btn btn-warning pull-left',"onclick"=>"return confirm('Вы действительно хотите сделать заказ \"ДУБЛЕМ\"')"]) ?>
			</p>
		</td>
		<td>
			<p>
				<?= Html::a('Клиент отказался', ['/intermode/ecommerce/default/reset-and-cancel-outbound', 'id' => $model->id], ['class' => 'btn btn-danger pull-right',"onclick"=>"return confirm('Вы действительно хотите отменить заказ как \"ОТКАЗ КЛИЕНТА\"')"]) ?>
			</p>
		</td>
	</tr>
</table>