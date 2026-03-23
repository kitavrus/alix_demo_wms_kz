<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
?>
<h1>Печатаем лист сборки для Subaru Auto, Hyundai Auto, Hyundai Truck,</h1>

<?= \yii\grid\GridView::widget([
    'dataProvider' => $dataProvider,
    'rowOptions'=> function ($model, $key, $index, $grid) {
        $class = '';
        if($model->expected_qty != $model->allocated_qty) {
            $class = 'color-indian-red';
        }
        if($model->expected_qty == $model->allocated_qty) {
            $class = 'color-dark-olive-green';
        }
        if($model->status == \common\modules\stock\models\Stock::STATUS_OUTBOUND_NEW) {
            $class = 'color-light-sky-blue';
        }
        return ['class'=>$class];
    },
    'columns' => [
        [
            'attribute'=>'action',
            'format'=>'raw',
            'value'=>function($data) {
				
				if(!in_array($data->id,[28822])) {
					return \yii\bootstrap\Html::a("Печатать",Url::toRoute(['print-picking-list','id'=>$data->id]),['class'=>'btn btn-success']);	
				}
				
                return '';
            },
        ],
        [
            'attribute' => 'order_number',
            'format' => 'html',
            'value' => function ($data) {
                return Html::tag('a', $data->order_number, ['href' => Url::to(['/outbound/report/view', 'id' => $data->id]), 'target' => '_blank']);
            },
        ],
        'description',
        [
            'attribute'=>'to_point_id',
            'value'=>function($data) use($storesArray) {
                return \common\overloads\ArrayHelper::getValue($storesArray,$data->to_point_id);
            },
        ],
        [
            'attribute'=>'status',
            'value'=>function($data){
                return $data->getStatusValue();
            },
        ],
        'expected_qty',
        'allocated_qty',
        'created_at:datetime',
    ],
]); ?>