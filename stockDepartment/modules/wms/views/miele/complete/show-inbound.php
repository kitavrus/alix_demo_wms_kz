<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
?>
<h1>Закрываем приходные накладные для MIELE</h1>

<?= \yii\grid\GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        [
            'attribute'=>'action',
            'format'=>'raw',
            'value'=>function($data){
                return \yii\bootstrap\Html::a("Закрыть",Url::toRoute(['done-inbound','id'=>$data->id]),['class'=>'btn btn-success']);
            },
        ],
        'order_number',
        [
            'attribute'=>'zone',
            'value'=>function($data){
                return $data->getZoneValue();
            },
        ],
        [
            'attribute'=>'status',
            'value'=>function($data){
                return $data->getStatusValue();
            },
        ],
        'expected_qty',
        'accepted_qty',
    ],
]); ?>