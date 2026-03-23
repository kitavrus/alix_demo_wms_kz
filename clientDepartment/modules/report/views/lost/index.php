<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use common\modules\stock\models\Stock;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\stock\models\StockSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('stock/titles', 'Search item');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lost-item-index">

    <h1><?= Html::encode($this->title) ?></h1>
<!--    --><?php //echo $this->render('_search-filter', ['model' => $searchModel]); ?>
    <?= Html::tag('a', Yii::t('buttons', 'Print all in excel'), ['class' => 'btn btn-danger ', 'style' => '', 'id' => 'print-all-lost-excel-bt','data-url-value'=>Url::to(['excel']),'href'=>Url::to(['excel'])]) ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'id' => 'lost-grid',
        'columns' => [
            ['class' => 'yii\grid\CheckboxColumn'],
            [
                'label' => 'Заказ',
                'attribute' => 'outbound_order_id',
                'value' => function($data) {
                    $r = '';
                   $outboundOrder = \common\modules\outbound\models\OutboundOrder::findOne($data->outbound_order_id);
                    if($outboundOrder) {
                        $r = $outboundOrder->order_number. ' / '.Yii::$app->formatter->asDate($outboundOrder->packing_date) ;
                    }
                    return $r;
                }
            ],
            'product_barcode',
            'product_model',
            [
                'attribute' => 'status',
                'value' => function($data){
                    return $data->getStatusValue();
                }
            ],
            [
                'attribute' => 'status_availability',
                'value' => function($data){
                    return $data->getAvailabilityStatusValue();
                }
            ],
            [
                'attribute' => 'status_lost',
                'value' => function($data){
                    return $data->getLostStatusValue();
                }
            ],
        ],
    ]); ?>

</div>
<script type="application/javascript">
    $(function() {
        $('#stocksearch-product_barcode').focus().select();
    })
</script>