<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\ecommerce\intermode\inbound\entities\EcommerceInboundSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Ecommerce Inbounds report';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ecommerce-inbound-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'attribute'=> 'id',
                'format'=> 'html',
                'value' => function ($data) { return Html::tag('a', $data->id, ['href'=>\yii\helpers\Url::to(['view', 'id' => $data->id]), 'target'=>'_blank']);},
            ],
            [
                'attribute'=> 'status',
                'format'=> 'html',
                'value' => function ($data) {
                    return \common\ecommerce\constants\InboundStatus::getValue($data->status);
                },
            ],

            'party_number',
            'order_number',
            'expected_box_qty',
            'accepted_box_qty',
            'expected_product_qty',
            'accepted_product_qty',
            'date_confirm:datetime',
            'client_id',
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]); ?>
</div>

<div>
<!--    --><?//= Html::tag('span',Yii::t('transportLogistics/buttons','Export to Excel'),['class' => 'btn btn-success','id'=>'report-order-export-btn', 'data-url'=>'/ecommerce/defacto/report/inbound-export-to-excel']) ?>
<!--    --><?//= Html::tag('span',Yii::t('transportLogistics/buttons','Export to Excel with Products'),['class' => 'btn btn-success','id'=>'report-order-export-full-btn', 'data-url'=>'/ecommerce/defacto/report/inbound-export-to-excel-with-products']) ?>
    <?= Html::tag('span',Yii::t('transportLogistics/buttons','Export to Excel for Dasten'),['class' => 'btn btn-warning','id'=>'report-order-export--for-dastan-btn', 'data-url'=>'/ecommerce/defacto/report/inbound-export-to-excel-for-dastan']) ?>
    <br />
</div>

<script type="text/javascript">
    $(function(){

        $('#report-order-export-btn').on('click',function() {
            window.location.href = $(this).data('url')+'?'+$('#inbound-order-search-form').serialize();
        });

        $('#report-order-export-full-btn').on('click',function() {
            window.location.href = $(this).data('url')+'?'+$('#inbound-order-search-form').serialize();
        });

        $('#report-order-export--for-dastan-btn').on('click',function() {
            window.location.href = $(this).data('url')+'?'+$('#inbound-order-search-form').serialize();
        });
    });
</script>