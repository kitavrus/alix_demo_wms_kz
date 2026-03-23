<?php
use yii\grid\GridView;
use yii\helpers\Html;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
?>
<?=
GridView::widget([
    'dataProvider' => new ActiveDataProvider([
        'query' => $model->getRegistryItems(),
        'sort' => false,
        'pagination' => false,

    ]),
    'filterModel' => false,
    'summary' => false,
    'columns' => [
        'id',
        [
            'label'=> 'Заказы',
            'value' => function ($data) { return $data->getExtraFieldValueByName('orders');},
        ],
        [

            'attribute' => 'route_from',
            'value' => function ($data) use ($storeArray) {return isset($storeArray[$data->route_from]) ? $storeArray[$data->route_from] : '-';},
        ],
        [

            'attribute' => 'route_to',
            'value' => function ($data) use ($storeArray) {return isset($storeArray[$data->route_to]) ? $storeArray[$data->route_to] : '-';},
        ],

        'weight:decimal',
        'volume:decimal',
        'places',

        ['class' => 'yii\grid\ActionColumn',
            //'template'=>'{update} {delete} {changelog}',
            'template'=>'{delete-item}',
            'buttons'=>[
                'delete-item'=> function ($url, $model, $key) {
                    return   Html::button(Yii::t('buttons', 'Delete'), [
                        'class' => 'btn btn-danger delete-item-btn',
                        'data-url' => Url::toRoute('delete-item'),
                        'data-id' => $model->id,
                    ]);
                },
            ]
        ],
    ],
]);
?>
