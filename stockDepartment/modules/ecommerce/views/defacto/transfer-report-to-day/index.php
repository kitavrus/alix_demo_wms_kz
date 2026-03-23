<?php

use common\ecommerce\constants\StockTransferStatus;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel common\ecommerce\entities\EcommerceStockSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

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
                'attribute'=> 'status_transfer',
                'format'=> 'html',
                'value' => function ($data) {
                    return StockTransferStatus::getValue($data->status_transfer);
                },
            ],
			[
				'attribute'=> 'transfer_id',
				'format'=> 'html',
				'value' => function ($data) use ($searchModel) {
					return $searchModel->getTransferById($data->transfer_id);
				},
			],
			'product_barcode',
			'transfer_outbound_box',
			'scan_out_datetime:datetime',
        ],
    ]); ?>
</div>

<div>
    <?= Html::tag('span',Yii::t('transportLogistics/buttons','Export to Excel'),['class' => 'btn btn-success','id'=>'report-order-export-to-day-btn', 'data-url'=>'/ecommerce/defacto/transfer-report-to-day/export-to-excel']) ?>
    <br />
</div>

<script type="text/javascript">
    $(function(){

        $('#report-order-export-to-day-btn').on('click',function() {
            window.location.href = $(this).data('url')+'?'+$('#transfer-order-search-to-day-form').serialize();
        });
    });
</script>