<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\ecommerce\entities\EcommerceOutboundSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Ecommerce Damage Report';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ecommerce-outbound-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
<!--    <div class="form-group">-->
<!--        --><?//= Html::a(Yii::t('buttons', 'Очистить поиск'), 'index', ['class' => 'btn btn-warning']) ?>
<!--    </div>-->
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
        'columns' => [ //             [['',''], 'string'],
//            'client_id',
            'product_barcode',
            [
                'attribute'=> 'reason_re_reserved',
                'format'=> 'html',
                'value' => function ($data) {

                    return  (new \common\ecommerce\constants\OutboundCancelStatus())->getValueForPartReReserved($data['reason_re_reserved']);
                },
            ],
            'order_re_reserved',
            [
                'attribute'=> 'condition_type',
                'format'=> 'html',
                'value' => function ($data) {

                    return  (new \common\ecommerce\constants\StockConditionType)->getConditionTypeValue($data['condition_type']);
                },
            ],
            'place_address_barcode',
            'box_address_barcode',
        ],
    ]); ?>
</div>
<div>
    <?= Html::tag('span',Yii::t('transportLogistics/buttons','Export to Excel'),['class' => 'btn btn-success','id'=>'report-order-export-btn', 'data-url'=>'/ecommerce/defacto/problem-report/export-to-excel']) ?>
    <br />
</div>

<script type="text/javascript">
    $(function(){
        $('#report-order-export-btn').on('click',function() {
            window.location.href = $(this).data('url')+'?'+$('#outbound-order-search-form').serialize();
        });
    });
</script>
