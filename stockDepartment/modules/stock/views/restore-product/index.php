<?php
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\stock\models\StockSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('stock/titles', 'Search item');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="search-item-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search-filter', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            [
                'label' => Yii::t('forms', 'Quantity'),
                'attribute' => 'qty',
				'value' => function($data) {
					return 1;
				}
            ],
            [
                'label' => Yii::t('stock/forms', 'Product barcode'),
                'attribute' => 'product_barcode',
            ],
            [
                'label' => Yii::t('stock/forms', 'Primary address'),
                'attribute' => 'primary_address',
            ],
            [
                'label' => Yii::t('stock/forms', 'Secondary address'),
                'attribute' => 'secondary_address',
            ],
            [
                'label' => Yii::t('stock/forms', 'Product model'),
                'attribute' => 'product_model',
            ],
            [
                'label' => Yii::t('stock/forms', 'Condition type'),
                'attribute' => 'condition_type',
                'value' => function($data) use ($conditionTypeArray){
                    return isset ($conditionTypeArray[$data['condition_type']]) ?$conditionTypeArray[$data['condition_type']] : '-';
                }
            ],
            [
                'label' => Yii::t('stock/forms', 'Status'),
                'value' => function($data) use ($statusArray){
                    return \yii\helpers\ArrayHelper::getValue($statusArray,$data['status']);
                }
            ],
            [
                'label' => Yii::t('stock/forms', 'Status availability'),
                'attribute' => 'status_availability',
                'value' =>  function($data) use ($availabilityStatusArray){
                    return \yii\helpers\ArrayHelper::getValue($availabilityStatusArray,$data['status_availability']);
                }
            ],
            'inventory_id',
            'inventory_primary_address',
            'inventory_secondary_address',
			[
				'label' =>"Комментарий",
				'attribute' => 'field_extra5',
				'value' =>  function($data) {
					return $data["field_extra5"];
				}
			],
			['class' => 'yii\grid\ActionColumn',
				'template'=>'{available-product} {blocked-product}',
				'buttons'=>[
					'available-product'=> function ($url, $model, $key) {
						return   Html::a(
								Yii::t('titles', 'Сделать товар доступным'),
								["/stock/restore-product/available-product","id"=>$model['id']],
								[
									'class' => 'btn btn-warning',
									'data' => [
										'confirm' => Yii::t('titles', 'Вы действительно хотите сделать товар доступным?'),
										'method' => 'post',
									],
								]
						);
					},
					'blocked-product'=> function ($url, $model, $key) {
						return   Html::a(
								Yii::t('titles', 'Заблокировать товар'),
								["/stock/restore-product/blocked-product","id"=>$model['id']],
								['class' => 'btn btn-danger  pull-right',
									'data' => [
										'confirm' => Yii::t('titles', 'Вы действительно хотите заблокировать?'),
										'method' => 'post',
									],
								]
						);
					},
				]
			],
        ],
    ]); ?>
</div>

<script type="text/javascript">
    $(function() {
        $('#stock-item-search-export-btn').on('click',function() {
            window.location.href = $(this).data('url')+'?'+$('#stock-item-search-form').serialize();
        });
    });
</script>