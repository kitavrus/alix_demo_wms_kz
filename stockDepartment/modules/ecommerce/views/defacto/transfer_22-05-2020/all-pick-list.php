<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
?>
<h1>Печатаем лист сборки для Трансфера E-commerce DEFACTO,</h1>

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
        if($model->status == \common\ecommerce\constants\TransferStatus::_NEW) {
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
            //            'checkboxOptions' => function ($data, $key, $index, $column) {
            //                return ['value' => $data['id'],];
            //            }
            ],
        [
            'attribute'=> 'id',
            'format'=> 'html',
            'value' => function ($data) {
                if($data->status == \common\ecommerce\constants\TransferStatus::_NEW) {
                    return Html::tag('a', "Пред. Резерв ", ['href'=>\yii\helpers\Url::to(['test-reserve', 'id' => $data->id]), 'target'=>'_blank', 'class'=>'btn btn-danger']);
                }
//
                return '';
            },

        ],
        'client_BatchId',
        'client_Status',
        'expected_box_qty',
        [
            'attribute'=>'status',
            'value'=>function($data){
                return \common\ecommerce\constants\TransferStatus::getValue($data->status);
            },
        ],
        'expected_qty',
        'allocated_qty',
        'created_at:datetime',
    ],
]); ?>

<div>
    <?= Html::tag('span', Yii::t('transportLogistics/buttons', 'Печать листа сборки'), ['class' => 'btn btn-success', 'id' => 'report-order-export-btn']) ?>
    <br/>
</div>

<script type="text/javascript">
    $(function () {
        $('#report-order-export-btn').on('click', function () {
            var keys = $('#grid-view-order-items').yiiGridView('getSelectedRows'),
                serialize = [];

            serialize.push({'name': 'ids', 'value': keys});

            if (keys.length < 1) {
                alert('Нужно выбрать хотябы одно заявку');
            } else {
                window.location.href = '/ecommerce/defacto/transfer/print?ids=' + keys;
            }
            console.info(serialize);
        });
    });
</script>
