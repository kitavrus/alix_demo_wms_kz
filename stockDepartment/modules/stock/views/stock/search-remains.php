<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\stock\models\StockSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('stock/titles', 'Остатки на складе');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="search-item-index" xmlns="http://www.w3.org/1999/html">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search-remains-filter', ['model' => $searchModel,'clientsArray' => $clientsArray,'conditionTypeArray'=>$conditionTypeArray]); ?>
    <strong>
    <?php echo "Кол-во коробов: ".$qtyBox."<br />" ?>
    <?php echo "Кол-во мест: ".$qtyAddress."<br />" ?>
	<?php echo "Кол-во товаров: ".$qtyProduct."<br />" ?>
    </strong>

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
            //'warehouse_id',
            //'product_id',
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
                'label' => Yii::t('stock/forms', 'Status availability'),
                'attribute' => 'status_availability',
                'value' =>  function($data) use ($availabilityStatusArray){
                    return isset ($availabilityStatusArray[$data['status_availability']]) ? $availabilityStatusArray[$data['status_availability']] : '-';
                }
            ],
            'inventory_id',
            'inventory_primary_address',
            'inventory_secondary_address',
        ],
    ]); ?>

</div>

<div>
    <?= Html::tag('span',
        Yii::t('transportLogistics/buttons','Экспорт в Exel краткий'),
        ['class' => 'btn btn-success','id'=>'stock-remains-search-export-btn', 'data-url'=>\yii\helpers\Url::to('/stock/stock/remains-export-to-excel')]) ?>
    <?= Html::tag('span',
        Yii::t('transportLogistics/buttons','Экспорт в Exel подробный'),
        ['class' => 'btn btn-warning','id'=>'stock-remains-search-export-detail-btn', 'data-url'=>yii\helpers\Url::to(['/stock/stock/remains-export-to-excel'])]) ?>
    <br />
</div>

<script type="text/javascript">
    $(function(){

        $('#stock-remains-search-export-btn,#stock-remains-search-export-detail-btn').on('click',function() {
            var detail='';
            if($(this).attr('id')=='stock-remains-search-export-detail-btn') {
                detail = '&detail=y';
            }

            window.location.href = $(this).data('url')+'?'+$('#stock-remains-search-form').serialize()+detail;
        });

    });
</script>
