<?php

use yii\helpers\Html;
use common\modules\transportLogistics\models\TlDeliveryProposal;
//use frontend\modules\client\models\Client;
//use frontend\modules\store\models\Store;
use app\modules\transportLogistics\transportLogistics;
use common\modules\transportLogistics\components\TLHelper;
use kartik\grid\GridView;
use kartik\grid\EditableColumn;
use kartik\grid\DataColumn;
use common\modules\client\models\ClientEmployees;
use clientDepartment\modules\client\components\ClientManager;


/* @var $this yii\web\View */
/* @var $searchModel clientDepartment\modules\transportLogistics\models\TlDeliveryProposalSearch */
/* @var $filterWidgetOptionDataRoute common\modules\store\models\Store */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('transportLogistics/titles', 'Tl Delivery Proposals');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="tl-delivery-proposal-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('transportLogistics/buttons', 'Create Tl Delivery Proposal' ), ['create'], ['class' => 'btn btn-success']) ?>

        <?= Html::a(Yii::t('transportLogistics/buttons', 'Очистить поиск'), ['index'], ['class' => 'btn btn-primary','style'=>'float:right; margin-left:10px;margin-top: -50px;']) ?>

        <!--        --><?php //if($isCountConfirm = TlDeliveryProposal::getCountIsWaitingConfirm()) { ?>
        <!--            --><?//= Html::a(Yii::t('transportLogistics/buttons', 'Ждут подтверждения').'  '.Html::tag('span',$isCountConfirm,['class'=>'label label-danger']).'', ['index','TlDeliveryProposalSearch[is_client_confirmed]'=>TlDeliveryProposal::IS_CLIENT_CONFIRMED_WAITING], ['class' => 'btn btn-warning','style'=>'float:right;']) ?>
        <!--        --><?php //} ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute'=> 'id',
                'options' => ['width'=>'20px'],

            ],
            [
                'class' => DataColumn::className(),
                'attribute' => 'route_from',
                'value' => function ($model) {
                    $value = TLHelper::getStoreArrayByClientID($model->client_id);
                    return isset ($value[$model->routeFrom->id]) ? $value[$model->routeFrom->id]:$model->routeFrom->name;
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => $filterWidgetOptionDataRoute,
//                    'data' => $searchModel::getRouteFromTo(),
                    'options' => [
                        'placeholder' => Yii::t('transportLogistics/forms', 'Select route'),
                    ],
                ],
            ],
            [
                'class' => DataColumn::className(),
                'attribute' => 'route_to',
                'value' => function ($model) {
                    $value = TLHelper::getStoreArrayByClientID($model->client_id);
                    return isset ($value[$model->routeTo->id]) ? $value[$model->routeTo->id]:$model->routeTo->name;
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => $filterWidgetOptionDataRoute,
//                    'data' => $searchModel::getRouteFromTo(),
                    'options' => [
                        'placeholder' => Yii::t('transportLogistics/forms', 'Select route'),
                    ],
                ],
            ],
            'shipped_datetime:datetime',
            'expected_delivery_date:datetime',
            'delivery_date:datetime',
            'mc',
            'kg',
            'number_places',

            [
                'attribute'=> 'cash_no',
                'filter'=> $searchModel->getPaymentMethodArray(),
                'value' => function ($data) {return TlDeliveryProposal::getPaymentMethodArray($data->cash_no);},

            ],
            'price_invoice',
            'price_invoice_with_vat',
            [
                'attribute'=> 'status',
                'filter'=> $searchModel::getStatusArray(),
                'value' => function ($data) {return TlDeliveryProposal::getStatusArray($data->status);},

            ],
            [
                'attribute'=> 'status_invoice',
                'filter'=> $searchModel::getInvoiceStatusArray(),
                'value' => function ($data) {return TlDeliveryProposal::getInvoiceStatusArray($data->status_invoice);},

            ],
            ['class' => 'yii\grid\ActionColumn',
                'template'=>'{view} {update} {print-ttn} {print-box-label} {delete}',
                'buttons'=>[
                    'view'=> function ($url, $model, $key) {
                        return Html::a(Yii::t('buttons','View'), $url,['class'=>'btn btn-primary btn-grid-action-column']).'<br />';
                    },
                    'update'=> function ($url, $model, $key) {
                        $button = '';
                        if(ClientManager::canUpdateDeliveryProposal($model)) {
                            $button = Html::a(Yii::t('buttons', 'Update'), $url, ['class' => 'btn btn-warning btn-grid-action-column']) . '<br />';
                        }
                        return  $button;
                    },
                    'print-ttn'=> function ($url, $model, $key) {
                        $button = '';
                        if(ClientManager::canPrintTTNDeliveryProposal($model)) {
                            $button = Html::a(Yii::t('buttons','Print TTN'), $url,['class'=>'btn btn-info btn-grid-action-column']).'<br />';
                        }
                        return  $button;
                    },
                    'print-box-label'=> function ($url, $model, $key) {
                        $button = '';
                        if(ClientManager::canPrintTTNDeliveryProposal($model)) {
                            $button = Html::a(Yii::t('buttons','печать <br /> этикеток'), $url,['class'=>'btn btn-info btn-grid-action-column']).'<br />';
                        }
                        return  $button;
                    },

                    'delete'=> function ($url, $model, $key) {
                        $button = '';
                        if(ClientManager::canDeleteDeliveryProposal($model)) {
                            $button =  Html::a(Yii::t('buttons', 'Delete'), ['delete', 'id' => $model->id], [
                                    'class' => 'btn btn-danger btn-grid-action-column',
                                    'data' => [
                                        'confirm' => Yii::t('titles', 'Are you sure you want to delete this item?'),
                                        'method' => 'post',
                                    ],
                                ]).'<br />';
                        }
                        return  $button;
                    },
                ]
            ],

        ],
    ]); ?>

</div>
