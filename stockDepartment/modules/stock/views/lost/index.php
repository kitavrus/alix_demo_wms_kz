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
    <?php echo $this->render('_search-filter', ['model' => $searchModel]); ?>

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
            'secondary_address',
            'primary_address',
            'product_barcode',
            'product_model',

//            [
//                'attribute' => 'condition_type',
//                'value' => function($data){
//                    return $data->getConditionTypeValue();
//                }
//            ],
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
            [
                'attribute'=>'actions',
                'label' => Yii::t('outbound/forms','Actions'),
                'format' => 'raw',
                'value' => function($model) use ($box) {
//                    $bt = '';
//                    if($model->status_lost==Stock::STATUS_LOST_PARTIAL) {
//                        $bt.= \yii\helpers\Html::tag('span', Yii::t('buttons', 'Full lost'),
//                            [
//                                'class' => 'btn btn-danger',
//                                'style' => ' margin-left:10px;',
//                                'id' => 'item-lost-full-bt',
//                                'data-url-value'=>Url::to(['item-lost?id='.$model->id])
//                            ]);
//                    }

                    $bt =\yii\helpers\Html::tag('span', Yii::t('buttons', 'Found'),
                        [
                            'class' => 'btn btn-success',
                            'style' => ' margin-left:10px;',
                            'id' => 'item-lost-found-bt',
                            'data-url-value'=>Url::to(['item-found','id'=>$model->id,'box'=>$box])
                        ]);
						
                    if(empty($model->stock_adjustment_id)) {
                        $bt .= \yii\helpers\Html::tag('span', Yii::t('buttons', 'Не найден'),
                            [
                                'class' => 'btn btn-danger',
                                'style' => ' margin-left:10px;',
                                'id' => 'item-lost-found-bt',
                                'data-url-value' => Url::to(['item-lost', 'id' => $model->id, 'box' => $box])
                            ]);
                    }						

                    return $bt;
                },
            ]
        ],
    ]); ?>

</div>

<script type="application/javascript">
    $(function() {
        $('#stocksearch-product_barcode').focus().select();
    })
</script>