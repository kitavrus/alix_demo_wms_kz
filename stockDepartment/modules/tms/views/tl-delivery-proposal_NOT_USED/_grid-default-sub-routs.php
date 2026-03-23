<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 25.09.2015
 * Time: 12:22
 */

use kartik\grid\GridView;
use yii\data\ActiveDataProvider;

?>

<?= GridView::widget([
    'dataProvider' => new ActiveDataProvider([
        'query' => $model->getSubRoutes(),
        'sort' => false,
    ]),
    'columns' => [
        [
            'attribute' => 'from_point_id',
            'value' => function ($data) use ($storeArray) {
                return isset($storeArray[$data->from_point_id]) ? $storeArray[$data->from_point_id] : '-';
            },
        ],
        [
            'attribute' => 'to_point_id',
            'value' => function ($data) use ($storeArray) {
                return isset($storeArray[$data->to_point_id]) ? $storeArray[$data->to_point_id] : '-';
            },
        ],
        [
            'label' => 'Субподрядчик и транспорт',
            'value' => function ($data) {
                return $car = $data->car ? $data->car->getDisplayTitle() : '-';
            },
        ],
        [
            'attribute' => 'transport_type',
            'value' => function ($data) {
                return $data->getTransportTypeValue();
            },
        ],
    ],
]);
?>