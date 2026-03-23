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
    <?php echo $this->render('_search', ['model' => $searchModel, 'clientStoreArray' =>$clientStoreArray ]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'id' => 'inbound-order-report',
        'columns' => [
            'id',
            [
                'attribute'=> 'parent_order_number',
                'format'=> 'html',
                'value' => function ($data) {
                    return Html::tag('a', $data->parent_order_number, ['href'=>Url::to(['view', 'id' => $data->id]), 'target'=>'_blank']);
                },
            ],
            [
                'attribute'=> 'order_number',
                'format'=> 'html',
                'value' => function ($data) {
                    $comment = trim($data->comments);
                    $comment = !empty($comment) ? " / ".$comment : '';
                    return Html::tag('a', $data->order_number.$comment, ['href'=>Url::to(['view', 'id' => $data->id]), 'target'=>'_blank']);
                },
            ],
            [
                'attribute' => 'from_point_id',
                'value' => function($data) use ($clientStoreArray){
                    if($data->from_point_id){
                        return isset ($clientStoreArray[$data->from_point_id]) ? $clientStoreArray[$data->from_point_id] : '-';
                    }
                    return '-';
                }
            ],
            [
                'attribute' => 'to_point_id',
                'value' => function($data) use ($clientStoreArray){
                    if($data->to_point_id){

                        return isset ($clientStoreArray[$data->to_point_id]) ? $clientStoreArray[$data->to_point_id] : '-';
                    }
                    return '-';
                }
            ],
            [
                'attribute'=> 'order_type',
                'value' => function ($data) {
                    return $data->getOrderTypeValue();
                },
            ],
             'expected_qty',
             'accepted_qty',
             'accepted_number_places_qty',
             'expected_number_places_qty',
             'created_at:datetime',
             'expected_datetime:datetime',
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

    <?= Html::tag('span',Yii::t('transportLogistics/buttons','Export to Exel'),['class' => 'btn btn-success','id'=>'report-order-export-btn', 'data-url'=>'/report/inbound/export-to-excel']) ?>
    <?= Html::tag('span',Yii::t('transportLogistics/buttons','Export differences to Excel'),['class' => 'btn btn-primary','id'=>'report-differences-btn', 'data-url'=>'/report/inbound/export-differences-to-excel']) ?>
    <?= Html::tag('span',Yii::t('transportLogistics/buttons','Подробный экспорт Excel'),['class' => 'btn btn-warning','id'=>'report-order-export-full-btn', 'data-url'=>'/report/inbound/export-to-excel-full']) ?>
    <?php
        if(\clientDepartment\modules\client\components\ClientManager::canExportPn() && $showExportPnButton){
            echo Html::tag('span',Yii::t('transportLogistics/buttons','Export Inbound'),['class' => 'btn btn-primary','id'=>'report-inbound-btn', 'data-url'=>'/report/inbound/export-pn']);
        }
     ?>
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

        $('#report-differences-btn').on('click',function() {
            window.location.href = $(this).data('url')+'?'+$('#inbound-orders-grid-search-form').serialize();
        });

        $('#report-inbound-btn').on('click',function() {
            window.location.href = $(this).data('url')+'?'+$('#inbound-orders-grid-search-form').serialize();
        });

    });
</script>
