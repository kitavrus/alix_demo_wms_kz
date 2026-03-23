<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 15.11.2019
 * Time: 13:17
 */
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\ecommerce\entities\EcommerceOutboundSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Найти товар на складе B2C';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ecommerce-outbound-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php  echo $this->render('_search-find-product-on-stock', ['model' => $searchModel]); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
			'place_address_barcode',
			'box_address_barcode',
			'product_barcode',
			'qty',
			'client_product_sku'
        ],
    ]); ?>
</div>
<div>
    <?= Html::tag('span',Yii::t('transportLogistics/buttons','Export to PDF'),['class' => 'btn btn-success','id'=>'report-order-export-btn', 'data-url'=>'/ecommerce/defacto/check-on-stock/print-find-product-on-stock']) ?>
    <?= Html::tag('span',Yii::t('transportLogistics/buttons','Export to Excel'),['class' => 'btn btn-warning','id'=>'report-order-export-excel-btn', 'data-url'=>'/ecommerce/defacto/check-on-stock/stock-export-to-excel']) ?>
    <br />
</div>

<script type="text/javascript">
    $(function(){

	    $('#report-order-export-btn').on('click',function() {
		    $('#find-product-on-stock-form').attr('action',$(this).data('url'));
		    $('#find-product-on-stock-form').submit();
	    });
	    $('#report-order-export-excel-btn').on('click',function() {
		    $('#find-product-on-stock-form').attr('action',$(this).data('url'));
		    $('#find-product-on-stock-form').submit();
	    });

        // $('#report-order-export-btn').on('click',function() {
        //     window.location.href = $(this).data('url')+'?'+$('#find-product-on-stock-form').serialize();
        // });
    });
</script>