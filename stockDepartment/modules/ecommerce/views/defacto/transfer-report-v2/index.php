<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\ecommerce\entities\EcommerceTransferSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $clientStoreArray array */

$this->title = Yii::t('app', 'Ecommerce Transfers');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ecommerce-transfer-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'attribute'=> 'id',
                'format'=> 'html',
                'value' => function ($data) { return Html::tag('a', $data->id, ['href'=>\yii\helpers\Url::to(['view', 'id' => $data->id]), 'target'=>'_blank']);},

            ],
            'client_BatchId',
            'client_Status',
			[
				'attribute'=>  'client_ToBusinessUnitId',
				'value'=>function ($model) {
					return \common\modules\store\models\Store::findClientStoreByShopCodeForECom($model->client_ToBusinessUnitId);
				}
			],
            'expected_box_qty',
            [
                'attribute'=> 'status',
                'format'=> 'html',
                'value' => function ($data) {
                    return \common\ecommerce\constants\TransferStatus::getValue($data->status);
                },
            ],
            'expected_qty',
            'allocated_qty',
            'accepted_qty',
            'print_picking_list_date:datetime',
            'begin_datetime:datetime',
            'end_datetime:datetime',
            'packing_date:datetime',
            'date_left_warehouse:datetime',
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]); ?>
</div>

<div>
    <?= Html::tag('span',Yii::t('transportLogistics/buttons','Export to Excel'),['class' => 'btn btn-success','id'=>'report-order-export-btn', 'data-url'=>'/ecommerce/defacto/transfer-report-v2/export-to-excel']) ?>
    <?= Html::tag('span',Yii::t('transportLogistics/buttons','Export to Excel LC'),['class' => 'btn btn-success','id'=>'report-order-export-box-lc-btn', 'data-url'=>'/ecommerce/defacto/transfer-report-v2/export-to-excel-box-lc']) ?>
    <?= Html::tag('span',Yii::t('transportLogistics/buttons','Export to Excel LC with Products'),['class' => 'btn btn-success','id'=>'report-order-export-box-lc-products-btn', 'data-url'=>'/ecommerce/defacto/transfer-report-v2/export-to-excel-box-lc-with-products']) ?>
    <?= Html::tag('span',Yii::t('transportLogistics/buttons','Export to Excel with Products'),['class' => 'btn btn-success','id'=>'report-order-export-full-btn', 'data-url'=>'/ecommerce/defacto/transfer-report-v2/export-to-excel-with-products']) ?>
	
	<?= Html::tag('span',Yii::t('transportLogistics/buttons','Export to Excel 10...'),['class' => 'btn btn-success','id'=>'report-order-export-box-ten-btn', 'data-url'=>'/ecommerce/defacto/transfer-report-v2/export-to-excel-box-ten']) ?>
		
    <br />
</div>

<script type="text/javascript">
    $(function(){

        $('#report-order-export-btn').on('click',function() {
            window.location.href = $(this).data('url')+'?'+$('#transfer-order-search-form').serialize();
        });

        $('#report-order-export-box-lc-btn').on('click',function() {
            window.location.href = $(this).data('url')+'?'+$('#transfer-order-search-form').serialize();
        });

        $('#report-order-export-full-btn').on('click',function() {
            window.location.href = $(this).data('url')+'?'+$('#transfer-order-search-form').serialize();
        });

        $('#report-order-export-box-lc-products-btn').on('click',function() {
            window.location.href = $(this).data('url')+'?'+$('#transfer-order-search-form').serialize();
        });
		
		$('#report-order-export-box-ten-btn').on('click',function() {
            window.location.href = $(this).data('url')+'?'+$('#transfer-order-search-form').serialize();
        });


    });
</script>