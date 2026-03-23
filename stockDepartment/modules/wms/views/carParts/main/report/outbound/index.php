<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 30.01.15
 * Time: 17:43
 */

use yii\helpers\Html;
use yii\helpers\Url;
use common\modules\store\models\Store;
use common\helpers\iHelper;
$this->title = Yii::t('outbound/titles', 'Report: outbound orders');
$this->params['breadcrumbs'][] = $this->title;
?>

<h1><?= Html::encode($this->title) ?></h1>
<?= $this->render('_search', ['model' => $searchModel, 'clientsArray' => $clientsArray]); ?>

<?= \yii\grid\GridView::widget([
    'id' => 'grid-view-order-items',
    'dataProvider' => $activeDataProvider,
    'rowOptions'=> function ($model, $key, $index, $grid) {
        $class = iHelper::getStockGridColor($model->status);
        return ['class'=>$class];
    },
    'columns' => [
        [
            'attribute'=> 'id',
            'format'=> 'html',
            'value' => function ($data) { return Html::tag('a', $data->id, ['href'=>Url::to(['/outbound/report/view', 'id' => $data->id]), 'target'=>'_blank']);},
        ],
        'order_number',
        [
            'attribute'=>'description',
            'label'=>'Комментарий',
        ],
        [
            'attribute'=>'to_point_id',
            'value'=>function($data)use ($storesArray) {
                return \common\overloads\ArrayHelper::getValue($storesArray,$data->to_point_id);
            },
        ],
        'expected_qty',
        [
            'attribute'=>'allocated_qty',
            'contentOptions' => function ($model, $key, $index, $column) {
                return ['id'=>'allocated-qty-cell-'.$model->id];
            }
        ],
        'accepted_qty',
        'created_at:datetime',
        'packing_date:datetime',
        'date_left_warehouse:datetime',
        [
            'attribute'=>'status',
            'value'=> function($model) {
                return $model->getStatusValue();
            }
        ],
    ],
]); ?>

<div>

    <?= Html::tag('span',Yii::t('transportLogistics/buttons','Export to Excel'),['class' => 'btn btn-success','id'=>'report-order-export-btn', 'data-url'=>'outbound-to-excel']) ?>
    <?= Html::tag('span',Yii::t('transportLogistics/buttons','Export to Excel Billing'),['class' => 'btn btn-success','id'=>'report-order-export-full-btn', 'data-url'=>'outbound-billing-to-excel']) ?>
    <?= Html::tag('span',Yii::t('transportLogistics/buttons','Export to Excel shipping list'),['class' => 'btn btn-warning','id'=>'report-order-export-ship-list-btn', 'data-url'=>'shipping-list-to-excel']) ?>
    <br />
</div>

<script type="text/javascript">
    $(function() {
        $('#report-order-export-btn').on('click',function() {
            window.location.href = $(this).data('url')+'?'+$('#outbound-orders-grid-search-form').serialize();
        });

        $('#report-order-export-full-btn').on('click',function() {
            window.location.href = $(this).data('url')+'?'+$('#outbound-orders-grid-search-form').serialize();
        });

        $('#report-order-export-ship-list-btn').on('click',function() {
            window.location.href = $(this).data('url')+'?'+$('#outbound-orders-grid-search-form').serialize();
        });
    });
</script>


