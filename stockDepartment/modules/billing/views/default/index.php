<?php

use yii\helpers\Html;
use common\modules\store\models\Store;
use kartik\grid\GridView;
use kartik\grid\EditableColumn;
use kartik\grid\DataColumn;
use common\modules\transportLogistics\components\TLHelper;
use app\modules\transportLogistics\transportLogistics;
use common\modules\billing\models\TlDeliveryProposalBilling;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel stockDepartment\modules\billing\models\TlDeliveryProposalBillingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = $title;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tl-delivery-proposal-billing-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('titles', 'Create billing'), ['create', 'tariffType'=>$tariffType], ['class' => 'btn btn-success']) ?>

    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute'=> 'id',
                'format'=> 'html',
                'value' => function ($data) { return Html::tag('a', $data->id, ['href'=>Url::to(['view', 'id' => $data->id]), 'target'=>'_blank']);},

            ],
            [
                'class' => DataColumn::className(),
                'attribute' => 'client_id',
                'value' => 'client.title',
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => $clientArray,
                    'options' => [
                        'placeholder' => Yii::t('forms', 'Select client'),
                    ],

                ],

            ],
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
            [
                'class' => DataColumn::className(),
                'attribute' => 'from_city_id',
                'value' => 'city.name',
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => $searchModel->getCityArray(),
                    'options' => [
                        'placeholder' => Yii::t('forms', 'Select city'),
                    ],
                ],

            ],
            [
                'class' => DataColumn::className(),
                'attribute' => 'to_city_id',
                'value' => 'cityTo.name',
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => $searchModel->getCityArray(),
                    'options' => [
                        'placeholder' => Yii::t('forms', 'Select city'),
                    ],
                ],

            ],
            [
                'class' => DataColumn::className(),
                'attribute' => 'route_from',
                'value' => function ($model) use ($storeArray) {
//                    $value = TLHelper::getStockPointArray($model->client_id);
//                    if($rt = $model->routeFrom) {
//                        return isset ($value[$model->routeFrom->id]) ? $value[$model->routeFrom->id]:$model->routeFrom->name;
//                    }
//                    return '-';
                    return isset ($storeArray[$model->route_from]) ? $storeArray[$model->route_from] : '-';
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => $storeArray,
//                    'data' => $searchModel::getRouteFromTo(),
                    'options' => [
                        'placeholder' => Yii::t('transportLogistics/titles', 'Select route'),
                    ],
                ],
            ],
            [
                'class' => DataColumn::className(),
                'attribute' => 'route_to',
                'value' => function ($model) use ($storeArray)  {
//                    $value = TLHelper::getStockPointArray($model->client_id);
//                    if($rt = $model->routeTo) {
//                        return isset ($value[$model->routeTo->id]) ? $value[$model->routeTo->id]:$model->routeTo->name;
//                    }
                    return isset ($storeArray[$model->route_to]) ? $storeArray[$model->route_to] : '-';
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => $storeArray,
//                    'data' => $searchModel::getRouteFromTo(),
                    'options' => [
                        'placeholder' => Yii::t('transportLogistics/titles', 'Select route'),
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
            ],[

                'attribute' => 'delivery_term_from',
                'value' => function ($model) {
                    return $model->delivery_term_from.' до '. $model->delivery_term_to;
                },
                //'filter' => $searchModel::getStatusArray(),
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
<div>
    <?php if(Yii::$app->request->get('tariffType') == TlDeliveryProposalBilling::TARIFF_TYPE_COMPANY_INDIVIDUAL){ ?>
    <?= Html::tag('span',Yii::t('transportLogistics/buttons','Export to Exel'),['class' => 'btn btn-success','id'=>'report-order-export-btn', 'data-url'=>'/billing/default/export-to-excel']) ?>
    <?= Html::tag('span',Yii::t('transportLogistics/buttons','Export to Exсel with condition'),['class' => 'btn btn-success','id'=>'report-order-if-export-btn', 'data-url'=>'/billing/default/export-to-excel']) ?>
    <?php } ?>
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