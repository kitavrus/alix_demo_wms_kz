<?php

use common\ecommerce\constants\OutboundStatus;
use yii\helpers\Html;
use yii\grid\GridView;
use common\helpers\iHelper;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel common\ecommerce\entities\EcommerceOutboundSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Ecommerce Outbounds Report';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ecommerce-outbound-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'id' => 'return-orders-list',
        'dataProvider' => $dataProvider,
        'rowOptions'=> function ($model, $key, $index, $grid) {
            $class = OutboundStatus::getStockGridColor($model->status);
            return ['class'=>$class];
        },
        'columns' => [
            [
                'attribute'=> 'id',
                'format'=> 'html',
                'value' => function ($data) { return Html::tag('a', $data->id, ['href'=> Url::to(['view', 'id' => $data->id]), 'target'=>'_blank']);},
            ],
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
    ]); ?>
</div>
<div>
    <?= Html::tag('span',Yii::t('transportLogistics/buttons','Export to Excel'),['class' => 'btn btn-success','id'=>'report-order-export-btn', 'data-url'=>'/ecommerce/defacto/return-report/export-to-excel']) ?>
    <?= Html::tag('span',Yii::t('transportLogistics/buttons','Export to Excel with Products'),['class' => 'btn btn-success','id'=>'report-order-export-full-btn', 'data-url'=>'/ecommerce/defacto/return-report/export-to-excel-with-products']) ?>
    <br />
</div>

<script type="text/javascript">
    $(function(){

        $('#report-order-export-btn').on('click',function() {
            window.location.href = $(this).data('url')+'?'+$('#return-report-order-search-form').serialize();
        });

        $('#report-order-export-full-btn').on('click',function() {
            window.location.href = $(this).data('url')+'?'+$('#return-report-order-search-form').serialize();
        });
    });
</script>