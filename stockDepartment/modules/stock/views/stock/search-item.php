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
			'inbound_client_box',
//            [
//                'label' => Yii::t('stock/forms', 'Status lost'),
//                'attribute' => 'status_lost',
//                'value' => function($data) use ($lostStatusArray){
//                    return isset ($lostStatusArray[$data['status_lost']]) ? $lostStatusArray[$data['status_lost']] : '-';
//                }
//            ],
        ],
    ]); ?>
</div>

<div>
    <?= Html::tag('span',
        Yii::t('transportLogistics/buttons','Экспорт в Excel краткий'),
        ['class' => 'btn btn-success','id'=>'stock-item-search-export-btn', 'data-url'=>\yii\helpers\Url::to('item-export-to-excel')]) ?>
</div>

<script type="text/javascript">
    $(function() {
        $('#stock-item-search-export-btn').on('click',function() {
            window.location.href = $(this).data('url')+'?'+$('#stock-item-search-form').serialize();
        });
    });
</script>