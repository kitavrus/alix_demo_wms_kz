<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\stock\models\ChangeAddressPlaceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'История размещений');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ecommerce-inbound-index">

	<h1><?= Html::encode($this->title) ?></h1>
	<?php  echo $this->render('_search', ['model' => $searchModel]); ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'columns' => [
			'from_barcode',
			'product_barcode',
			'to_barcode',
			'message',
			'created_at:datetime',
			'updated_at:datetime',
		],
	]); ?>
</div>

<div>
	<?= Html::tag('span',Yii::t('transportLogistics/buttons','Export to Excel'),['class' => 'btn btn-warning','id'=>'report-order-export-btn', 'data-url'=>'/stock/change-address-place/export-to-excel']) ?>
</div>

<script type="text/javascript">
	$(function(){

		$('#report-order-export-btn').on('click',function() {
			window.location.href = $(this).data('url')+'?'+$('#change-address-search-form').serialize();
		});
	});
</script>
