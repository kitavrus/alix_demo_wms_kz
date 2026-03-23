<?php

use yii\helpers\Url;
use yii\bootstrap\Modal;
use kartik\datecontrol\DateControl;
use yii\helpers\Html;
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
        <p>
            <?= Html::a(Yii::t('transportLogistics/buttons', 'Очистить поиск'), ['delivery-order'], ['class' => 'btn btn-primary']) ?>
        </p>

        <?= GridView::widget([
            'id' => 'delivery-proposal-grid-view',
            'dataProvider' => $activeDataProvider,
            'filterModel' => $searchModel,
            'floatHeader' => true,
            'rowOptions'=> function ($model, $key, $index, $grid) {
                $class = $model->getGridColor();
                return ['class'=>$class];
            },
            'columns' => [
                [
                    'attribute'=>'action',
                    'format'=>'raw',
                    'value'=>function($data){
                        return \yii\bootstrap\Html::a("Печать ТТН",Url::toRoute(['carParts/main/delivery-order/print-ttn','id'=>$data->id]),['class'=>'btn btn-success']);
                    },
                ],
                [
                    'attribute'=> 'id',
                    'format'=> 'html',
                    'value' => function ($data) { return Html::tag('a', $data->id, ['href'=>Url::to(['/tms/default/view', 'id' => $data->id]), 'target'=>'_blank']);},
                ],
                [
                    'attribute'=> 'client_ttn',
                ],
                [
                    'attribute'=> 'orders',
                    'format'=> 'raw',
                    'value' => function ($data) {
                        return str_replace(', ','<br />',$data->getExtraFieldValueByName('orders'));
                    },
                ],
                [
                    'class' => DataColumn::className(),
                    'attribute' => 'client_id',
                    'format'=> 'html',
                    'value' => function($data) use ($clientsArray){
                        if(isset($clientsArray[$data->client_id])){
                            return $clientsArray[$data->client_id];//, ['href'=>Url::to(['/client/default/view', 'id' => $data->client_id]), 'target'=>'_blank']);
//                            return Html::tag('a', $clientsArray[$data->client_id], ['href'=>Url::to(['/client/default/view', 'id' => $data->client_id]), 'target'=>'_blank']);
                        }
                        return Yii::t('titles', 'Not set');
                    },
                    'filterType' => GridView::FILTER_SELECT2,
                    'filterWidgetOptions' => [
                        'data' => $clientsArray,
                        'options' => [
                            'placeholder' => Yii::t('transportLogistics/forms', 'Select client')
                        ],
                    ],
                ],
                [
                    'class' => DataColumn::className(),
                    'attribute' => 'route_to',
                    'value' => function ($data) use ($storesArray) {return isset($storesArray[$data->route_to]) ? $storesArray[$data->route_to] : '-';},
                    'filterType' => GridView::FILTER_SELECT2,
                    'filterWidgetOptions' => [
                        'data' => $storesArray,
                        'options' => [
                            'placeholder' => Yii::t('transportLogistics/titles', 'Select route'),
                        ],
                    ],
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
//                ['class' => 'yii\grid\ActionColumn',
//                    'template'=>'{view} {update}',
//                    'buttons'=>[]
//                ],
            ],
        ]); ?>
    </div>