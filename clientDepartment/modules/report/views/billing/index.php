<?php

use yii\helpers\Html;
use common\modules\store\models\Store;
use kartik\grid\GridView;
use kartik\grid\EditableColumn;
use kartik\grid\DataColumn;
use common\modules\transportLogistics\components\TLHelper;
use app\modules\transportLogistics\transportLogistics;

/* @var $this yii\web\View */
/* @var $searchModel stockDepartment\modules\billing\models\TlDeliveryProposalBillingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('titles', 'Billings');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tl-delivery-proposal-billing-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

<!--    <p>-->
        <?= Html::a(Yii::t('titles', 'Clear search'), ['index'], ['class' => 'btn btn-primary','style'=>'float:right; margin-left:10px;margin-top: -50px;']) ?>
<!--    </p>-->

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],
            'id',
//            [
//                'class' => DataColumn::className(),
//                'attribute' => 'client_id',
//                'value' => 'client.title',
//                'filterType' => GridView::FILTER_SELECT2,
//                'filterWidgetOptions' => [
//                    'data' => $searchModel->getClientArray(),
//                    'options' => [
//                        'placeholder' => Yii::t('forms', 'Select client'),
//                    ],
//
//                ],
//
//            ],
//            [
//                'class' => DataColumn::className(),
//                'attribute' => 'country_id',
//                'value' => 'country.name',
//                'filterType' => GridView::FILTER_SELECT2,
//                'filterWidgetOptions' => [
//                    'data' => $searchModel->getCountryArray(),
//                    'options' => [
//                        'placeholder' => Yii::t('forms', 'Select city'),
//                    ],
//                ],
//
//            ],
//            [
//                'class' => DataColumn::className(),
//                'attribute' => 'region_id',
//                'value' => 'region.name',
//                'filterType' => GridView::FILTER_SELECT2,
//                'filterWidgetOptions' => [
//                    'data' => $searchModel->getRegionArray(),
//                    'options' => [
//                        'placeholder' => Yii::t('forms', 'Select city'),
//                    ],
//                ],
//
//            ],
//            [
//                'class' => DataColumn::className(),
//                'attribute' => 'city_id',
//                'value' => 'city.name',
//                'filterType' => GridView::FILTER_SELECT2,
//                'filterWidgetOptions' => [
//                    'data' => $searchModel->getCityArray(),
//                    'options' => [
//                        'placeholder' => Yii::t('forms', 'Select city'),
//                    ],
//                ],
//
//            ],
            [
                'class' => DataColumn::className(),
                'attribute' => 'route_from',
                'value' => function ($model) {
                    $value = TLHelper::getStoreArrayByClientID($model->client_id);
                    if($rt = $model->routeFrom) {
                        return isset ($value[$model->routeFrom->id]) ? $value[$model->routeFrom->id]:$model->routeFrom->name;
                    }
                    return '-';
//                    return isset ($value[$model->routeFrom->id]) ? $value[$model->routeFrom->id]:$model->routeFrom->name;
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TLHelper::getStoreArrayByClientID(),
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
                    if($rt = $model->routeTo) {
                        return isset ($value[$model->routeTo->id]) ? $value[$model->routeTo->id]:$model->routeTo->name;
                    }
                    return '-';
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TLHelper::getStoreArrayByClientID(),
//                    'data' => $searchModel::getRouteFromTo(),
                    'options' => [
                        'placeholder' => Yii::t('transportLogistics/forms', 'Select route'),
                    ],
                ],
            ],
//             'mc',
//             'kg',
//             'number_places',
             'price_invoice_with_vat:currency',
            [
//                'class' => EditableColumn::className(),
//                'editableOptions' => [
//                    'inputType' => 'dropDownList',
//                    'data' =>$searchModel::getRuleTypeArray(),
//                    'placement' => 'left',
//                ],
                'attribute' => 'rule_type',
                'value' => function ($model) {
                    return $model->getRuleType();
                },
                'filter' => $searchModel::getRuleTypeArray(),
            ],
            [
//                'class' => EditableColumn::className(),
//                'editableOptions' => [
//                    'inputType' => 'dropDownList',
//                    'data' =>$searchModel::getStatusArray(),
//                    'placement' => 'left',
//                ],
                'attribute' => 'status',
                'value' => function ($model) {
                    return $model->getStatus();
                },
                'filter' => $searchModel::getStatusArray(),
            ],

//            ['class' => 'yii\grid\ActionColumn'],
            ['class' => 'yii\grid\ActionColumn',
                'template'=>'{view}',
                'buttons'=>[
                    'view'=> function ($url, $model, $key) {
                        return Html::a(Yii::t('buttons','View'), $url,['class'=>'btn btn-primary btn-grid-action-column']).'<br />';
                    },
//                    'update'=> function ($url, $model, $key) {
//                        $button = '';
//                        if(ClientManager::canUpdateDeliveryProposal($model)) {
//                            $button = Html::a(Yii::t('buttons', 'Edit'), $url, ['class' => 'btn btn-warning btn-grid-action-column']) . '<br />';
//                        }
//                        return  $button;
//                    },
//                    'print-ttn'=> function ($url, $model, $key) {
//                        $button = '';
//                        if(ClientManager::canPrintTTNDeliveryProposal($model)) {
//                            $button = Html::a(Yii::t('buttons','Print TTN'), $url,['class'=>'btn btn-info btn-grid-action-column']).'<br />';
//                        }
//                        return  $button;
//                    },
//                    'print-box-label'=> function ($url, $model, $key) {
//                        $button = '';
//                        if(ClientManager::canPrintTTNDeliveryProposal($model)) {
//                            $button = Html::a(Yii::t('buttons','печать <br /> этикеток'), $url,['class'=>'btn btn-info btn-grid-action-column']).'<br />';
//                        }
//                        return  $button;
//                    },

//                    'delete'=> function ($url, $model, $key) {
//                        $button = '';
//                        if(ClientManager::canDeleteDeliveryProposal($model)) {
//                            $button =  Html::a(Yii::t('buttons', 'Delete'), ['delete', 'id' => $model->id], [
//                                    'class' => 'btn btn-danger btn-grid-action-column',
//                                    'data' => [
//                                        'confirm' => Yii::t('titles', 'Are you sure you want to delete this item?'),
//                                        'method' => 'post',
//                                    ],
//                                ]).'<br />';
//                        }
//                        return  $button;
//                    },
                ]
            ],
        ],
    ]); ?>
</div>
<div>
   <?= Html::tag('span',Yii::t('transportLogistics/buttons','Export to Exel'),['class' => 'btn btn-success','id'=>'report-order-export-btn', 'data-url'=>'/report/billing/export-to-excel']) ?>
   <?= Html::tag('span',Yii::t('transportLogistics/buttons','Export to Exсel with condition'),['class' => 'btn btn-success','id'=>'report-order-if-export-btn', 'data-url'=>'/report/billing/export-to-excel']) ?>
    <br />
</div>

<script type="text/javascript">
    $(function(){

        $('#report-order-export-btn').on('click',function() {
            var filterQuery = $('#w0-filters').find('.form-control').serialize();
            window.location.href = $(this).data('url')+'?'+filterQuery;
        });

        $('#report-order-if-export-btn').on('click',function() {
            var filterQuery = $('#w0-filters').find('.form-control').serialize();
            window.location.href = $(this).data('url')+'?if=1&'+filterQuery;
        });

    });
</script>
