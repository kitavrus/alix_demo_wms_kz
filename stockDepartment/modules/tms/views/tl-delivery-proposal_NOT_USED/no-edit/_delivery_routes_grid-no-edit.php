<?php
use kartik\grid\GridView;
use common\modules\transportLogistics\components\TLHelper;
use kartik\grid\EditableColumn;
use yii\helpers\Url;
?>
<?=
GridView::widget([
    'striped'=>false,
    'dataProvider' => $dataProviderProposalRoutes,
    'afterRow' => function ($model, $key, $index, $grid) {

        return $this->render('_route_expenses-grid-view-after-row-no-edit',['model'=>$model,'key'=>$key, 'index'=>$index, 'grid'=>$grid]);

    },
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'attribute' => 'route_from',
            'value' => function ($model) {
                $value = TLHelper::getStockPointArray();
                // return isset ($value[$model->routeFrom->id]) ? $value[$model->routeFrom->id]:$model->routeFrom->name;
                return isset ($value[$model->route_from]) ? $value[$model->route_from]:'-NONE-';

            },
            'contentOptions'=>['style'=>'background-color: #66afe9 !important']
        ],
        [
            'attribute' => 'route_to',
            'value' => function ($model) {
                $value = TLHelper::getStockPointArray();
                // return isset ($value[$model->routeTo->id]) ? $value[$model->routeTo->id]:$model->routeTo->name;
                return isset ($value[$model->route_to]) ? $value[$model->route_to]:'-NONE-';
            },
            'contentOptions'=>['style'=>'background-color: #66a000 !important']
        ],
        [
            'attribute' => 'transportation_type',
            'value' => function ($model) {

                return $model->getTransportationTypeValue();
            },
        ],
        [
            'attribute'=> 'shipped_datetime',
            'format' =>'date',

        ],
        [
//            'class' => EditableColumn::className(),
//            'editableOptions' => [
//                'inputType' =>'dropDownList',
//                'data' =>TlDeliveryProposal::getPaymentMethodArray(),
//                'formOptions'=>[
//                    'action'=>'edit-by-field-route'
//                ],
//            ],
            'attribute'=> 'cash_no',
            'value' => function ($model) {return $model->getPaymentMethodValue();},
//            'contentOptions'=>['style'=>'background-color: #66afe9 !important']

        ],
        [
//            'class' => EditableColumn::className(),
//            'editableOptions' => [
//                'inputType' =>'dropDownList',
//                'data' =>TlDeliveryRoutes::getStatusArray(),
//                'formOptions'=>[
//                    'action'=>'edit-by-field-route'
//                ],
//            ],
            'attribute' => 'status',
//            'refreshGrid' => true,
            'value' => function ($model) {
                return $model->getStatusValue();
            },
//            'contentOptions'=>['style'=>'background-color: #66afe9 !important']
        ],
        [
//            'class' => EditableColumn::className(),
//            'editableOptions' => [
//                'inputType' =>'dropDownList',
//                'data' => TlDeliveryProposal::getInvoiceStatusArray(),
//                'formOptions'=>[
//                    'action'=>'edit-by-field-route'
//                ],
//            ],
            'attribute' => 'status_invoice',
            'value' => function ($model) {
                return $model->getInvoiceStatusValue();
            },
        ],
        [
//            'class' => EditableColumn::className(),
            'attribute'=> 'price_invoice',
//            'editableOptions' => ['inputType' =>'\kartik\money\MaskMoney',
//                'formOptions'=>[
//                    'action'=>'edit-by-field-route'
//                ],
//
//            ],
            'format'=>'currency',
//            'contentOptions'=>['style'=>'background-color: #66afe9 !important']

        ],

//        ['class' => 'yii\grid\ActionColumn',
//            'template' => ' {update} {delete} ', //{add-order}
////            'buttons' => [
////                'add-order' => function ($url, $model, $key) {
////                    return Html::a(Yii::t('transportLogistics/buttons', 'Add order'), ['add-order', 'id' => $model->id], ['class' => 'btn btn-primary', 'style' => 'float:right;',]);
////                },
////
////            ],
//            'urlCreator' => function ($action, $model, $key, $index) {
//
//                $params = ['id' => $model->id];
//                $params[0] = $action . '-route';
//
//                return Url::toRoute($params);
//            }
//        ],
    ],
]); ?>