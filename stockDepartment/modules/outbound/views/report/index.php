<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 30.01.15
 * Time: 17:43
 */

use yii\helpers\Html;
use yii\helpers\Url;
use common\modules\store\models\Store;
use common\helpers\iHelper;
$this->title = Yii::t('outbound/titles', 'Report: outbound orders');
$this->params['breadcrumbs'][] = $this->title;
?>

<h1><?= Html::encode($this->title) ?></h1>
<?= $this->render('_search', ['model' => $searchModel, 'clientsArray' => $clientsArray,'clientStoreArray'=>$clientStoreArray]); ?>

<?= \yii\grid\GridView::widget([
    'id' => 'grid-view-order-items',
    'dataProvider' => $dataProvider,
    'rowOptions'=> function ($model, $key, $index, $grid) {
        $class = iHelper::getStockGridColor($model->status);
        return ['class'=>$class];
    },
    'columns' => [
	     [
                'label'=> 'action',
                'format'=> 'html',
                //'noWrap'=> true,
                'value' => function ($data) {
                    return '<h4 class="pull-left">'.Html::tag('a', 'АКТ ППТ', ['href'=>Url::to(['print-box-kg-list', 'id' => $data->id]),'class'=>'label label-default','style'=>'margin:5px; display: block;']).'</h4>';
                },
            ],
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
        'parent_order_number',
        'order_number',
        [
            'attribute'=>  'to_point_title',
            'value'=>function ($model) use ($clientStoreArray) {
                return \yii\helpers\ArrayHelper::getValue($clientStoreArray,$model->to_point_id);
            }
        ],
        'expected_qty',
        [
            'attribute'=>'allocated_qty',
            'contentOptions' => function ($model, $key, $index, $column) {
                return ['id'=>'allocated-qty-cell-'.$model->id];
            }
        ],
        'accepted_qty',
        'packing_date:datetime',
        'date_left_warehouse:datetime',
        'date_delivered:datetime',
        [
            'attribute'=>'status',
            'value'=> function($model) {
                return $model->getStatusValue();
            }
        ],
        [
            'attribute'=>'cargo_status',
            'value'=> function($model) {
                return $model->getCargoStatusValue();
            }
        ],
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
		[
				'attribute'=>'actions',
				'label' => Yii::t('outbound/forms','Actions'),
				'format' => 'raw',
				'value' => function($model) {
					return \yii\helpers\Html::a(
								Yii::t('outbound/buttons', 'Повторно отправить API'),
								Url::to(['/wms/defacto/outbound-v2/resend-api?orderId='.$model->id]),
								[
									'class' => 'btn btn-primary resend-api-data',
									'style' => ' margin-left:10px; display:none;',
									'id' => 'outbound-print-bt-'.$model->id,
								]
						);
				},
		]
    ],
]); ?>

<div>

    <?= Html::tag('span',Yii::t('transportLogistics/buttons','Export to Exel'),['class' => 'btn btn-success','id'=>'report-order-export-btn', 'data-url'=>'/outbound/report/export-to-excel']) ?>
    <?= Html::tag('span',Yii::t('transportLogistics/buttons','Export Items to Excel'),['class' => 'btn btn-success','id'=>'report-order-export-full-btn', 'data-url'=>'/outbound/report/export-to-excel-plus-product']) ?>
    <br />
</div>

<script type="text/javascript">
    $(function(){

		$('body').on('keyup', function (e) {
			if (e.which == 80) {
				$(".resend-api-data").show();
			}
		});

        $('#report-order-export-btn').on('click',function() {
            window.location.href = $(this).data('url')+'?'+$('#outbound-orders-grid-search-form').serialize();
        });

        $('#report-order-export-full-btn').on('click',function() {
            window.location.href = $(this).data('url')+'?'+$('#outbound-orders-grid-search-form').serialize();
        });

    });
</script>


