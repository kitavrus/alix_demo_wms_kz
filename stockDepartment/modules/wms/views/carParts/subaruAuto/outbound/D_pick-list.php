<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
?>
<h1>Печатаем лист сборки для Subaru Auto</h1>

<?= \yii\grid\GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        [
            'attribute'=>'action',
            'format'=>'raw',
            'value'=>function($data){
                return \yii\bootstrap\Html::a("Печатать",Url::toRoute(['print-picking-list','id'=>$data->id]),['class'=>'btn btn-success']);
            },
        ],
        'to_point_title',
        'order_number',
//        [
//            'attribute'=>'zone',
//            'value'=>function($data){
//                return $data->getZoneValue();
//            },
//        ],
        [
            'attribute'=>'status',
            'value'=>function($data){
                return $data->getStatusValue();
            },
        ],
        'expected_qty',
        'accepted_qty',
        'allocated_qty',
    ],
]); ?>