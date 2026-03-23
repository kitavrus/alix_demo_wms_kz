<?php

use app\modules\intermode\controllers\ecommerce\outbound\domain\constants\OutboundStatus;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
?>
<h1>Печатаем лист сборки для E-commerce INTERMODE,</h1>

<?= \yii\grid\GridView::widget([
    'id' => 'grid-view-order-items',
    'dataProvider' => $dataProvider,
    'rowOptions'=> function ($model, $key, $index, $grid) {
        $class = '';
        if($model->expected_qty != $model->allocated_qty) {
            $class = 'color-indian-red';
        }
        if($model->expected_qty == $model->allocated_qty) {
            $class = 'color-dark-olive-green';
        }
//        if($model->status == OutboundStatus::getNEW()) {
//            $class = 'color-light-sky-blue';
//        }

        if($model->status == OutboundStatus::getPRINTED_PICKING_LIST()) {
            $class = 'color-light-sky-blue';
        }
        return ['class'=>$class];
    },
    'columns' => [
      [
          'class' => 'yii\grid\SerialColumn',
      ],
        [
            'class' => 'yii\grid\CheckboxColumn',
        ],
        [
            'attribute' => 'order_number',
            'format' => 'html',
            'value' => function ($data) {
//                return Html::tag('a', $data->order_number, ['href' => Url::to(['/ecommerce/intermode/outbound/report/view', 'id' => $data->id]), 'target' => '_blank']);
                return Html::tag('a', $data->order_number, ['href' => Url::to(['/intermode/ecommerce/outbound/report/view', 'id' => $data->id]), 'target' => '_blank']);
            },
        ],
//        'client_Priority',
        [
            'attribute'=>'status',
            'value'=>function($data){
                return OutboundStatus::getValue($data->status);
            },
        ],
        'expected_qty',
        'allocated_qty',
		'client_ShipmentSource',
        'created_at:datetime',
    ],
	
]); ?>

<div>
    <?= Html::tag('span', Yii::t('transportLogistics/buttons', 'Print'), ['class' => 'btn btn-success', 'id' => 'report-order-export-btn']) ?>
    <br/>
</div>

<script type="text/javascript">
    $(function () {
        $('#report-order-export-btn').on('click', function () {
            var keys = $('#grid-view-order-items').yiiGridView('getSelectedRows'),
                serialize = [];

            serialize.push({'name': 'ids', 'value': keys});

            if (keys.length < 1) {
                alert('Нужно выбрать хотя бы одно заявку');
            } else {
               // window.location.href = '/ecommerce/intermode/outbound/picking/print?ids=' + keys;
                window.location.href = '/intermode/ecommerce/outbound/picking/print?ids=' + keys;
            }
            console.info(serialize);
        });
    });
</script>
