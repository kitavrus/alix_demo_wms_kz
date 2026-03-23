<?php

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
        'id' => 'outbound-orders-list',
        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
        'rowOptions'=> function ($model, $key, $index, $grid) {
            $class = \common\ecommerce\constants\OutboundStatus::getStockGridColor($model->status);
            return ['class'=>$class];
        },
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute'=> 'id',
                'format'=> 'html',
                'value' => function ($data) { return Html::tag('a', $data->id, ['href'=>\yii\helpers\Url::to(['outbound-view', 'id' => $data->id]), 'target'=>'_blank']);},

            ],
            [
                'attribute'=> 'client_ReferenceNumber',
                'label'=> 'ТТН',
                'format'=> 'html',
                'value' => function ($data) { return $data->client_ReferenceNumber; },

            ],
            'external_order_number',
            'order_number',
            [
                'attribute'=> 'Этикетки',
                'format'=> 'html',
                'value' => function ($data) {

                    $link = '';
                    if(!empty($data->path_to_cargo_label_file) ) {
                        $link .= Html::tag('a', 'Маленькая', ['href'=> Url::to(['/ecommerce/defacto/outbound/print-cargo-label','id'=>$data->id]), 'target'=>'_blank']);

                        $link .= ' / ';
                    } elseif(!empty($data->path_to_order_doc)) {
                    	if($data->client_ShipmentSource != "LamodaKazakhistan") {
							$link .= Html::tag('a', 'Получить повторно маленькую', [
								'href'=> Url::to(['/ecommerce/defacto/outbound/resend-get-cargo-label','orderNumber'=>$data->order_number]),
								'class'=>'btn btn-danger',
								'target'=>'_blank',
							]);

							if($data->client_ShipmentSource == "KaspiKazakhistan" && !empty($data->external_order_number)) {
								$link .= ' / ';
								$link .= Html::tag('a', 'Маленькая Kaspi', [
										'href' => 'http://cdn.ayensoftware.com/kaspikz/5366/e-' . $data->external_order_number . '.pdf',
									'class'=>'btn btn-info',
									'target' => '_blank'
								]);
							}

							$link .= ' / ';
	                    }
                    }
                    if(!empty($data->path_to_order_doc) ) {
                        $link .= Html::tag('a', 'Большая', ['href'=> Url::to(['/ecommerce/defacto/outbound/print-waybill','id'=>$data->id]), 'target'=>'_blank']);
                    }
                    if(!empty($data->allocated_qty) ) {
                        if(!empty($link)) {
                            $link .= ' / ';
                        }
                        $link .= Html::tag('a', 'Лист сборки', ['href' => Url::to(['/ecommerce/defacto/picking/print-picking-list-no-reserve', 'id' => $data->id]), 'target' => '_blank']);
                    }
                    return $link;
                },
            ],
            'expected_qty',
            'allocated_qty',
            'accepted_qty',
            //'place_expected_qty',
            //'place_accepted_qty',
            //'mc',
//            'kg',
            [
                'attribute'=> 'status',
                'format'=> 'html',
                'value' => function ($data) {
                    return \common\ecommerce\constants\OutboundStatus::getValue($data->status);
                },
            ],
            //'first_name',
            //'middle_name',
            //'last_name',
            //'customer_name',
            //'phone_mobile1',
            //'phone_mobile2',
            //'email:email',
            //'country',
            //'region',
            'city',
            //'zip_code',
            //'street',
            //'house',
            //'building',
            //'entrance',
            //'flat',
            //'intercom',
            //'floor',
            //'elevator',
            'customer_address',
            //'customer_comment:ntext',
            //'ttn:ntext',
            //'payment_method',
            //'payment_status',
            //'data_created_on_client',
            //'print_picking_list_date',
            //'begin_datetime:datetime',
            //'end_datetime:datetime',
            'packing_date:datetime',
            'date_left_warehouse:datetime',
            //'date_delivered_to_customer',
//            'client_CargoCompany',
            'client_Priority',
//            'client_ShippingCountryCode',
//            'client_ShippingCity',
            'client_ShipmentSource',
            'client_PackMessage:ntext',
            'client_GiftWrappingMessage:ntext',
            //'created_user_id',
            //'updated_user_id',
            'created_at:datetime',
            'updated_at:datetime',
            //'deleted',

//            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
<div>

    <?= Html::tag('span',Yii::t('transportLogistics/buttons','Export to Excel'),['class' => 'btn btn-success','id'=>'report-order-export-btn', 'data-url'=>'/ecommerce/defacto/report/outbound-export-to-excel']) ?>
    <?= Html::tag('span',Yii::t('transportLogistics/buttons','Export to Excel with Products'),['class' => 'btn btn-success','id'=>'report-order-export-full-btn', 'data-url'=>'/ecommerce/defacto/report/outbound-export-to-excel-with-products']) ?>
	<?= Html::tag('span',Yii::t('transportLogistics/buttons','Export to Excel for Dastan'),['class' => 'btn btn-success','id'=>'report-order-export-dastan-btn', 'data-url'=>'/ecommerce/defacto/report/outbound-export-to-excel-dastan']) ?>
    <br />
</div>

<script type="text/javascript">
    $(function(){

        $('#report-order-export-btn').on('click',function() {
            window.location.href = $(this).data('url')+'?'+$('#outbound-order-search-form').serialize();
        });

        $('#report-order-export-full-btn').on('click',function() {
            window.location.href = $(this).data('url')+'?'+$('#outbound-order-search-form').serialize();
        });

        $('#report-order-export-dastan-btn').on('click',function() {
            window.location.href = $(this).data('url')+'?'+$('#outbound-order-search-form').serialize();
        });
		
    });
</script>