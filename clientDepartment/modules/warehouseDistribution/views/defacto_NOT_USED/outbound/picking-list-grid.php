<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 10.02.15
 * Time: 14:58
 */
//use yii\helpers\Html;
//use yii\helpers\ArrayHelper;
?>
<?= $this->render('_search', ['model' => $searchModel]); ?>
<?= \yii\grid\GridView::widget([
//    'id' => 'grid-view-order-items',
    'dataProvider' => $dataProvider,
//    'filterModel' => $searchModel,
//    'layout'=>'{items}',
//    'pager'=>false,
//    'sorter'=>false,
//    'filterUrl' =>\yii\helpers\Url::toRoute('get-sub-order-grid'),
    'columns' => [

        'barcode',
        [
            'attribute'=>'status',
            'value'=>function($data){
                return $data->getStatusValue();
            },
        ],
        [
            'attribute'=>'employee_id',
            'value'=>function($data){
                if($employee = $data->employee){
                    return $employee->first_name .' '.$employee->last_name;
                }
                return '-';
            },
        ],
        [
            'label' => Yii::t('outbound/forms', 'Creation date'),
            'value' => function($data){
                return Yii::$app->formatter->asDatetime($data->created_at);
            }
        ],
//        'expected_qty',
//        [
//            'attribute'=>'allocated_qty',
//            'contentOptions' => function ($model, $key, $index, $column) {
//                return ['id'=>'allocated-qty-cell-'.$model->id];
//            }
//        ]
//        ['class' => 'yii\grid\ActionColumn'],
    ],
]); ?>