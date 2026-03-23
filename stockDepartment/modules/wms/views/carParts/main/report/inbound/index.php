<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel stockDepartment\modules\inbound\models\InboundOrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('inbound/titles', 'Report: inbound orders');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="inbound-order-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel, 'clientsArray' => $clientsArray]); ?>

    <?= GridView::widget([
        'dataProvider' => $activeDataProvider,
        'id' => 'inbound-order-report',
        'columns' => [
            [
                'attribute'=> 'id',
                'format'=> 'html',
                'value' => function ($data) { return Html::tag('a', $data->id, ['href'=>Url::to(['/inbound/report/view', 'id' => $data->id]), 'target'=>'_blank']);},
            ],
            [
                'attribute'=>'client_id',
                'value'=>function($data) use ($clientsArray){
                    if(isset($clientsArray[$data->client_id])){
                        return $clientsArray[$data->client_id];
                    }
                    return '-';
                },
            ],
            [
                'attribute'=> 'order_number',
                'format'=> 'html',
                'value' => function ($data) { return Html::tag('a', $data->order_number, ['href'=>Url::to(['/inbound/report/view', 'id' => $data->id]), 'target'=>'_blank']);},
            ],
             'comments',
             'expected_qty',
             'accepted_qty',
             'created_at:datetime',
             'begin_datetime:datetime',
             'date_confirm:datetime',
            [
                'attribute'=>'status',
                'filter' => $searchModel->getStatusArray(),
                'value'=>function($model){
                    return $model->getStatusValue();
                },
            ],
        ],
    ]); ?>

</div>

<div>
    <?= Html::tag('span',Yii::t('transportLogistics/buttons','Export to Excel'),['class' => 'btn btn-success','id'=>'report-order-export-btn', 'data-url'=>'inbound-to-excel']) ?>
    <?= Html::tag('span',Yii::t('transportLogistics/buttons','Export to Excel Billing'),['class' => 'btn btn-success','id'=>'report-order-export-full-btn', 'data-url'=>'inbound-billing-to-excel']) ?>
    <?= Html::tag('span',Yii::t('transportLogistics/buttons','Export to Excel Diff'),['class' => 'btn btn-success','id'=>'report-diff-order-export-full-btn', 'data-url'=>'inbound-diff-to-excel']) ?>
	<?= Html::tag('span',Yii::t('transportLogistics/buttons','Куда разместили'),['class' => 'btn btn-danger','id'=>'report-put-away-btn', 'data-url'=>'inbound-put-away-to-excel']) ?>
    <br />
</div>

<script type="text/javascript">
    $(function(){

        $('#report-order-export-btn').on('click',function() {
            window.location.href = $(this).data('url')+'?'+$('#inbound-orders-grid-search-form').serialize();
        });

        $('#report-order-export-full-btn').on('click',function() {
            window.location.href = $(this).data('url')+'?'+$('#inbound-orders-grid-search-form').serialize();
        });

        $('#report-diff-order-export-full-btn').on('click',function() {
            window.location.href = $(this).data('url')+'?'+$('#inbound-orders-grid-search-form').serialize();
        });
		
		
        $('#report-put-away-btn').on('click',function() {
            window.location.href = $(this).data('url')+'?'+$('#inbound-orders-grid-search-form').serialize();
        });
		

    });
</script>
