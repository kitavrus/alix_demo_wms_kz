<?php
use yii\helpers\Html;
use yii\helpers\Url;
use \common\modules\store\models\Store;

/* @var $this yii\web\View */
?>
<h1>Печатаем лист сборки для Трансфера E-commerce DEFACTO,</h1>

<?= Html::a(Yii::t('transportLogistics/buttons', 'Загружаем новые трансферы'),
    ['get-batches'],
    ['class' => 'btn btn-warning pull-right',
     'data-confirm' => Yii::t('yii', 'Вы действительно хотите загрузить новые трансферы?'),
     'data-method' => 'get',
    ]) ?>
<br />

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
//            [
//            'class' => 'yii\grid\CheckboxColumn',
//            ],
        [
            'attribute'=> 'id',
            'format'=> 'html',
            'value' => function ($data) {
//                if($data->status == \common\ecommerce\constants\TransferStatus::_NEW) {
//                    return Html::tag('a', "Пред. Резерв ", ['href'=>\yii\helpers\Url::to(['test-reserve', 'id' => $data->id]), 'target'=>'_blank', 'class'=>'btn btn-danger']);
//                }
//                if($data->status == \common\ecommerce\constants\TransferStatus::_NEW) {
                    return Html::tag('a', "Проверка или лист сборки", ['href'=>\yii\helpers\Url::to(['pre-reserved', 'id' => $data->id]), 'target'=>'_blank', 'class'=>'btn btn-danger']);
//                }
//                return '';
            },
        ],
        'client_BatchId',
		[
			'attribute'=>'client_ToBusinessUnitId',
			'value'=>function($data){
				return Store::findClientStoreByShopCodeForECom($data->client_ToBusinessUnitId);
			},
		],
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
                alert('Нужно выбрать хотя бы одно заявку');
            } else {
                window.location.href = '/ecommerce/defacto/transfer-v2/print?ids=' + keys;
            }
            console.info(serialize);
        });
    });
</script>
