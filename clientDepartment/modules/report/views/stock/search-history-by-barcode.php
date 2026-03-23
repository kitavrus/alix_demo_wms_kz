<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 19.09.2015
 * Time: 10:43
 */
use yii\helpers\Html;
use yii\grid\GridView;

$this->title = Yii::t('stock/titles', 'Search item');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="search-item-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search-history-filter', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'label' => Yii::t('stock/forms', 'Product barcode'),
                'attribute' => 'product_barcode',
            ],
            [
                'label' => Yii::t('stock/forms', 'Product model'),
                'attribute' => 'product_model',
            ],
            [
                'label' => Yii::t('stock/forms', 'Inbound order'),
                'attribute' => 'inbound_order_id',
                'value' => function ($data) {
                    $inboundOrder = '-';
                    if($i = \common\modules\inbound\models\InboundOrder::findOne($data['inbound_order_id'])) {
                        $inboundOrder = $i->order_number;
                    }
                    return $inboundOrder;
                }
            ],
            [
                'label' => Yii::t('stock/forms', 'Outbound order'),
                'format'=> 'html',
                'attribute' => 'outbound_order_id',
                'value' => function ($data) {
                    $show = '-';
                    if($o = \common\modules\outbound\models\OutboundOrder::findOne($data['outbound_order_id'])) {
                        $outboundOrderNumber = $o->parent_order_number.' - '.$o->order_number;
                        $show = Html::tag('a', $outboundOrderNumber, ['href'=>\yii\helpers\Url::to(['/report/outbound/view', 'id' => $o->id]), 'target'=>'_blank']);
                    } else {
						if($o = \common\ecommerce\entities\EcommerceOutbound::findOne($data['ecom_outbound_id'])) {
							$outboundOrderNumber = $o->order_number;
							$show = $outboundOrderNumber;
						}
                    }


                    return $show;
                }
            ],

            [
                'label' => Yii::t('stock/forms', 'Outbound order store'),
                'format'=> 'html',
                'attribute' => 'outbound_order_store',
                'value' => function ($data) use($clientStoreArray) {
                    $show = '-';

                    if($o = \common\modules\outbound\models\OutboundOrder::findOne($data['outbound_order_id'])) {
                        $outboundStoreName = '';
                        if(!empty($o->to_point_id) && isset ($clientStoreArray[$o->to_point_id])) {
                            $outboundStoreName =  $clientStoreArray[$o->to_point_id];
                        }

                        $show = Html::tag('a', $outboundStoreName, ['href'=>\yii\helpers\Url::to(['/report/outbound/view', 'id' => $o->id]), 'target'=>'_blank']);
                    }

                    return $show;
                }
            ],
//            [
//                'label' => Yii::t('stock/forms', 'Condition type'),
//                'attribute' => 'condition_type',
//                'value' => function ($data) use ($conditionTypeArray) {
//                    return isset($conditionTypeArray[$data['condition_type']]) ? $conditionTypeArray[$data['condition_type']] : '-';
//                }
//            ],
            [
                'label' =>  Yii::t('inbound/forms', 'Qty'),
                'attribute' => 'qty',
            ],

        ],
    ]); ?>

</div>


<?= Html::tag('span',Yii::t('transportLogistics/buttons','Export to Exel'),['class' => 'btn btn-success','id'=>'report-order-export-btn', 'data-url'=>'/report/stock/history-export-to-excel']) ?>
<script type="text/javascript">
    $(function(){

        $('#report-order-export-btn').on('click',function() {
            window.location.href = $(this).data('url')+'?'+$('#stock-index-filter').serialize();
        });

    });
</script>