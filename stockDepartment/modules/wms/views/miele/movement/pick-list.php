<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
?>
<h1>Печатаем лист сборки для MIELE</h1>

<?= \yii\grid\GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        [
            'attribute'=>'action',
            'format'=>'raw',
            'value'=>function($data){
                return \yii\bootstrap\Html::a("Печатать",Url::toRoute(['print-pick-list','id'=>$data->id]),['class'=>'btn btn-success']);
            },
        ],
        'order_number',
        [
            'attribute'=>'from_zone',
            'value'=>function($data){
                return \common\modules\movement\models\Movement::getZoneValue($data->from_zone);
            },
        ],
        [
            'attribute'=>'to_zone',
            'value'=>function($data){
                return \common\modules\movement\models\Movement::getZoneValue($data->to_zone);
            },
        ],
        [
            'attribute'=>'status',
            'value'=>function($data){
                return $data->getStatusValue();
            },
        ],
        'expected_qty',
//        'accepted_qty',
//        'allocated_qty',
    ],
]); ?>