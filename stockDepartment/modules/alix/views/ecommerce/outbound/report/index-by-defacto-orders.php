<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\helpers\iHelper;

/* @var $this yii\web\View */
/* @var $searchModel common\ecommerce\entities\EcommerceOutboundSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Для Дастана :-)';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ecommerce-outbound-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search-by-defacto-orders', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'id' => 'outbound-orders-list',
        'dataProvider' => $dataProvider,
        'rowOptions'=> function ($model, $key, $index, $grid) {
            $class = \common\ecommerce\constants\OutboundStatus::getStockGridColor($model->status);
            return ['class'=>$class];
        },
        'columns' => [
            [
                'attribute'=> 'id',
                'format'=> 'html',
                'value' => function ($data) { return Html::tag('a', $data->id, ['href'=>\yii\helpers\Url::to(['outbound-view', 'id' => $data->id]), 'target'=>'_blank']);},

            ],
            [
                'attribute'=> 'client_ReferenceNumber',
                'label'=> 'ТТН',
                'format'=> 'html',
                'value' => function ($data) { return $data->client_ReferenceNumber; },

            ],
            'order_number',
            [
                'attribute'=> 'Этикетки',
                'format'=> 'html',
                'value' => function ($data) {

                    $link = '';
                    if(!empty($data->path_to_cargo_label_file) ) {
                        $link .= Html::tag('a', 'Маленькая', ['href'=>\yii\helpers\Url::to(['/ecommerce/defacto/outbound/print-cargo-label','id'=>$data->id]), 'target'=>'_blank']);
                        $link .= ' / ';
                    } elseif(!empty($data->path_to_order_doc)) {
                        $link .= Html::tag('a', 'Получить повторно маленькую', [
                            'href'=>\yii\helpers\Url::to(['/ecommerce/defacto/outbound/resend-get-cargo-label','orderNumber'=>$data->order_number]),
                            'class'=>'btn btn-danger',
                            'target'=>'_blank',
                        ]);
                        $link .= ' / ';
                    }

                    if(!empty($data->path_to_order_doc) ) {
                        $link .= Html::tag('a', 'Большая', ['href'=>\yii\helpers\Url::to(['/ecommerce/defacto/outbound/print-waybill','id'=>$data->id]), 'target'=>'_blank']);
                    }
                    return $link;
                },
            ],
            'expected_qty',
            'allocated_qty',
            'accepted_qty',
            [
                'attribute'=> 'status',
                'format'=> 'html',
                'value' => function ($data) {
                    return \common\ecommerce\constants\OutboundStatus::getValue($data->status);
                },
            ],
            'city',
            'customer_address',
            'packing_date:datetime',
            'date_left_warehouse:datetime',
            'client_Priority',
            'client_ShipmentSource',
            'client_PackMessage:ntext',
            'client_GiftWrappingMessage:ntext',
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]); ?>
</div>
<div>

    <?= Html::tag('span',Yii::t('transportLogistics/buttons','Export to Excel'),['class' => 'btn btn-success','id'=>'report-order-export-btn', 'data-url'=>'/ecommerce/defacto/report/outbound-export-to-excel?forDastan=1']) ?>
<!--    --><?//= Html::tag('span',Yii::t('transportLogistics/buttons','Export to Excel with Products'),['class' => 'btn btn-success','id'=>'report-order-export-full-btn', 'data-url'=>'/ecommerce/defacto/report/outbound-export-to-excel-with-products?for-dastan=1']) ?>
    <br />
</div>

<script type="text/javascript">
    $(function(){

        $('#report-order-export-btn').on('click',function() {
            $('#outbound-order-search-form').attr('action',$(this).data('url'));
            $('#outbound-order-search-form').submit();
        });
    });
</script>