<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\ecommerce\entities\EcommerceOutboundSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Ecommerce Stock Report';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ecommerce-outbound-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>
<!--    <div class="form-group">-->
<!--        --><?//= Html::a(Yii::t('buttons', 'Очистить поиск'), 'on-stock', ['class' => 'btn btn-warning']) ?>
<!--    </div>-->
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
        'columns' => [
//            'client_id',
            'product_barcode',
            'qty',
            [
                'attribute'=> 'condition_type',
                'format'=> 'html',
                'value' => function ($data) {
                    return  (new \common\ecommerce\constants\StockConditionType)->getConditionTypeValue($data['condition_type']);
                },
            ],
            'place_address_barcode',
            'box_address_barcode',
            'client_product_sku',
			'inventory_id',
            'inventory_box_address_barcode',
            'inventory_place_address_barcode',
        ],
    ]); ?>
</div>
<div>
    <?= Html::tag('span',Yii::t('transportLogistics/buttons','Export to Excel'),['class' => 'btn btn-success','id'=>'report-order-export-btn', 'data-url'=>'/ecommerce/defacto/report/stock-export-to-excel']) ?>
    <br />
</div>

<script type="text/javascript">
    $(function(){

        $('#report-order-export-btn').on('click',function() {
            window.location.href = $(this).data('url')+'?'+$('#on-stock-form').serialize();
        });
    });
</script>