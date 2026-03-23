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
//            'client_id',
            'product_barcode',
            'box_address_barcode',
            'place_address_barcode',
            'qty',
            [
                'attribute'=> 'status_availability',
                'format'=> 'html',
                'value' => function ($data) {
                    return  (new \common\ecommerce\constants\StockAvailability())->getValue($data['status_availability']);
                },
            ],
            [
                'attribute'=> 'status_outbound',
                'format'=> 'html',
                'value' => function ($data) {
                    return  (new \common\ecommerce\constants\StockOutboundStatus())->getValue($data['status_outbound']);
                },
            ],
            [
                'attribute'=> 'condition_type',
                'format'=> 'html',
                'value' => function ($data) {

                    return  (new \common\ecommerce\constants\StockConditionType)->getConditionTypeValue($data['condition_type']);
                },
            ],
        ],
    ]); ?>
</div>

<div>

    <?= Html::tag('span',Yii::t('transportLogistics/buttons','Export to PDF'),['class' => 'btn btn-success','id'=>'report-order-export-btn', 'data-url'=>'/ecommerce/defacto/report/print-find-product-on-stock']) ?>
    <br />
</div>

<script type="text/javascript">
    $(function(){

        $('#report-order-export-btn').on('click',function() {
            window.location.href = $(this).data('url')+'?'+$('#find-product-on-stock-form').serialize();
        });
    });
</script>
