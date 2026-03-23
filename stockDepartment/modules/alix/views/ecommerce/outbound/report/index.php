<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\helpers\iHelper;
use yii\helpers\Url;
use stockDepartment\modules\intermode\controllers\ecommerce\outbound\domain\constants\OutboundStatus;

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
            $class = OutboundStatus::getStockGridColor($model->status);
            return ['class'=>$class];
        },
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute'=> 'id',
                'format'=> 'html',
                'value' => function ($data) { return Html::tag('a', $data->id, ['href'=>\yii\helpers\Url::to(['view', 'id' => $data->id]), 'target'=>'_blank']);},

            ],
//            [
//                'attribute'=> 'client_ReferenceNumber',
//                'label'=> 'ТТН',
//                'format'=> 'html',
//                'value' => function ($data) { return $data->client_ReferenceNumber; },
//
//            ],
//            'external_order_number',
            'order_number',
            [
                'attribute'=> 'Этикетки',
                'format'=> 'html',
                'value' => function ($data) {

                    $link = '';
//                    if(!empty($data->path_to_cargo_label_file) ) {
//                        $link .= Html::tag('a', 'Маленькая', ['href'=> Url::to(['/ecommerce/defacto/outbound/print-cargo-label','id'=>$data->id]), 'target'=>'_blank']);
//
//                        $link .= ' / ';
//                    } elseif(!empty($data->path_to_order_doc)) {
//                    	if($data->client_ShipmentSource != "LamodaKazakhistan") {
//							$link .= Html::tag('a', 'Получить повторно маленькую', [
//								'href'=> Url::to(['/ecommerce/defacto/outbound/resend-get-cargo-label','orderNumber'=>$data->order_number]),
//								'class'=>'btn btn-danger',
//								'target'=>'_blank',
//							]);

//							if($data->client_ShipmentSource == "KaspiKazakhistan" && !empty($data->external_order_number)) {
//								$link .= ' / ';
//								$link .= Html::tag('a', 'Маленькая Kaspi', [
//										'href' => 'http://cdn.ayensoftware.com/kaspikz/5366/e-' . $data->external_order_number . '.pdf',
//									'class'=>'btn btn-info',
//									'target' => '_blank'
//								]);
//							}
//
//							$link .= ' / ';
//	                    }
//                    }
//                    if(!empty($data->path_to_order_doc) ) {
//                        $link .= Html::tag('a', 'Большая', ['href'=> Url::to(['/ecommerce/defacto/outbound/print-waybill','id'=>$data->id]), 'target'=>'_blank']);
//                    }
                    if(!empty($data->allocated_qty) ) {
//                        if(!empty($link)) {
//                            $link .= ' / ';
//                        }
                        $link .= Html::tag('a', 'Лист сборки', ['href' => Url::to(['/intermode/ecommerce/outbound/picking/print-picking-list-no-reserve', 'id' => $data->id]), 'target' => '_blank']);
                    }
                    return $link;
                },
            ],
            'expected_qty',
            'allocated_qty',
            'accepted_qty',
			'client_ShipmentSource',
            [
                'attribute'=> 'status',
                'format'=> 'html',
                'value' => function ($data) {
                    return OutboundStatus::getValue($data->status);
                },
            ],
            'packing_date:datetime',
            'date_left_warehouse:datetime',
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]); ?>
</div>
<div>

    <?= Html::tag('span',Yii::t('transportLogistics/buttons','Export to Excel'),['class' => 'btn btn-success','id'=>'report-order-export-btn', 'data-url'=>'/intermode/ecommerce/outbound/report/outbound-export-to-excel']) ?>
    <?= Html::tag('span',Yii::t('transportLogistics/buttons','Export to Excel with Products'),['class' => 'btn btn-success','id'=>'report-order-export-full-btn', 'data-url'=>'/intermode/ecommerce/outbound/report/outbound-export-to-excel-with-products']) ?>
<!--	--><?//= Html::tag('span',Yii::t('transportLogistics/buttons','Export to Excel for Dastan'),['class' => 'btn btn-success','id'=>'report-order-export-dastan-btn', 'data-url'=>'/ecommerce/defacto/report/outbound-export-to-excel-dastan']) ?>
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