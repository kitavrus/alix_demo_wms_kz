<?php

use yii\helpers\Url;
use yii\bootstrap\Modal;
//use stockDepartment\assets\DpAsset;
use kartik\datecontrol\DateControl;
use yii\helpers\Html;
//use yii\grid\GridView;
use common\modules\transportLogistics\models\TlDeliveryProposal;
use common\modules\client\models\Client;
use common\modules\store\models\Store;
use app\modules\transportLogistics\transportLogistics;
use kartik\grid\GridView;
use kartik\grid\EditableColumn;
use kartik\grid\DataColumn;
use common\modules\transportLogistics\components\TLHelper;


/* @var $this yii\web\View
 * @var $searchModel stockDepartment\modules\transportLogistics\models\TlDeliveryProposalSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('transportLogistics/titles', 'Tl Delivery Proposals');
$this->params['breadcrumbs'][] = $this->title;

?>
    <div class="tl-delivery-proposal-index">
        <?= GridView::widget([
            'id' => 'delivery-proposal-grid-view',
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'floatHeader' => true,
            'rowOptions'=> function ($model, $key, $index, $grid) {
                $class = $model->getGridColor();
                return ['class'=>$class];
            },
            'columns' => [
                [
                    'attribute'=> 'id',
                    'format'=> 'html',
                    'value' => function ($data) { return Html::tag('a', $data->id, ['href'=>Url::to(['view', 'id' => $data->id]), 'target'=>'_blank']);},

                ],
                [
                    'attribute'=> 'orders',
                    'format'=> 'raw',
                    'value' => function ($data) {
                        return str_replace(', ','<br />',$data->getExtraFieldValueByName('orders'));
                    },
                ],
                [
                    'class' => EditableColumn::className(),
                    'editableOptions' => [
                        'inputType' =>'dropDownList',
                        'data' => TlDeliveryProposal::getDeliveryTypeArray(),
                        'formOptions'=>[
                            'action'=>'edit-by-field'
                        ],
                        'placement' => 'left',
                    ],
                    'refreshGrid' => true,
                    'attribute'=> 'delivery_type',
                    'filter'=> $searchModel::getDeliveryTypeArray(),
                    'value' => function ($data) {return $data->getDeliveryTypeValue(); },

                ],
                [
                    'class' => EditableColumn::className(),
                    'editableOptions' => [
                        'inputType' =>'dropDownList',
                        'data' =>TlDeliveryProposal::getStatusArray(),
                        'afterInput'=> function ($form, $widget) {
                            echo $form->field($widget->model, 'delivery_date')->widget(DateControl::className(), [
                                'type'=>DateControl::FORMAT_DATETIME,
                                'options'=>['id'=>'delivery_date'.$widget->model->id,]

                            ]);
                        },
                        'formOptions'=>[
                            'action'=>'status-edit-by-field',
                        ],
                    ],
                    'attribute'=> 'status',
                    'filter'=> $searchModel::getStatusArray(),
                    'value' => function ($data) { return TlDeliveryProposal::getStatusArray($data->status);},
                ],

                [
                    'class' => DataColumn::className(),
                    'attribute' => 'client_id',
                    'format'=> 'html',
                    'value' => function($data) use ($clientArray){
                        if(isset($clientArray[$data->client_id])){
                            return Html::tag('a', $clientArray[$data->client_id], ['href'=>Url::to(['/client/default/view', 'id' => $data->client_id]), 'target'=>'_blank']);
                        }
                        return Yii::t('titles', 'Not set');
                    },
                    'filterType' => GridView::FILTER_SELECT2,
                    'filterWidgetOptions' => [
                        'data' => $clientArray,
                        'options' => [
                            'placeholder' => Yii::t('transportLogistics/forms', 'Select client')
                        ],
                    ],
                ],

                [
                    'class' => DataColumn::className(),
                    'attribute' => 'route_from',
                    'value' => function ($data) use ($storeArray) {return isset($storeArray[$data->route_from]) ? $storeArray[$data->route_from] : '-';},
                    'filterType' => GridView::FILTER_SELECT2,
                    'filterWidgetOptions' => [
                        'data' => $storeArray,
                        'options' => [
                            'placeholder' => Yii::t('transportLogistics/titles', 'Select route'),
                        ],
                    ],
                ],
                [
                    'class' => DataColumn::className(),
                    'attribute' => 'route_to',
                    'value' => function ($data) use ($storeArray) {return isset($storeArray[$data->route_to]) ? $storeArray[$data->route_to] : '-';},
                    'filterType' => GridView::FILTER_SELECT2,
                    'filterWidgetOptions' => [
                        'data' => $storeArray,
                        'options' => [
                            'placeholder' => Yii::t('transportLogistics/titles', 'Select route'),
                        ],
                    ],
                ],
                [
                    'class' => DataColumn::className(),
                    'attribute' => 'city_to',
                    'label' => 'Направление',
                    'value' => function ($data) { return isset($data->routeTo->city) ? $data->routeTo->city->name : '-';},
                    'filterType' => GridView::FILTER_SELECT2,
                    'filterWidgetOptions' => [
                        'data' => $routeDirectionArray,
                        'options' => [
                            'placeholder' => '',
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ],
                ],

                [
                    'class' => DataColumn::className(),
                    'attribute' => 'shipped_datetime',
                    'value' => function($date){
                        return !empty($date->shipped_datetime) ? Yii::$app->formatter->asDatetime($date->shipped_datetime) : '-';
                    },
                    'filterType' => GridView::FILTER_DATE_RANGE,
                    'filterWidgetOptions' => [
                        'convertFormat'=>true,
//                    'hideInput'=>true,
                        'pluginOptions'=>[
                            'locale'=>[
                                'separator'=> ' / ',
                                'format'=>'Y-m-d',
                            ]
                        ]
                    ],
                ],
                [
                    'attribute'=>'mc_kg_np',
                    'label'=>'M3&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;Кг&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;Места&nbsp;&nbsp;&nbsp;',
                    'encodeLabel'=>false,
                    'value'=>function($data) {
                        return $data->mc_actual.' / '.$data->kg_actual.' / '.$data->number_places_actual;
                    },
                ],
                ['class' => 'yii\grid\ActionColumn',
                    'template'=>'{view} {update}',
                    'buttons'=>[]
                ],
            ],
        ]); ?>
    </div>