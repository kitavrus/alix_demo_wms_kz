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
        <?= Html::a(Yii::t('titles', 'Clear search'), ['index'], ['class' => 'btn btn-primary','style'=>'float:right; margin-left:10px;margin-top: -50px;']) ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            [
                'class' => DataColumn::className(),
                'attribute' => 'route_from',
                'value' => function ($model) use ($filterWidgetOptionDataRoute) {
                    $value = $filterWidgetOptionDataRoute;
                    if($rt = $model->routeFrom) {
                        return isset ($value[$model->routeFrom->id]) ? $value[$model->routeFrom->id]:$model->routeFrom->name;
                    }
                    return '-';
//                    return isset ($value[$model->routeFrom->id]) ? $value[$model->routeFrom->id]:$model->routeFrom->name;
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
                'value' => function ($model) use ($filterWidgetOptionDataRoute) {
                    $value = $filterWidgetOptionDataRoute;
                    if($rt = $model->routeTo) {
                        return isset ($value[$model->routeTo->id]) ? $value[$model->routeTo->id]:$model->routeTo->name;
                    }
                    return '-';
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => $filterWidgetOptionDataRoute,
                    'options' => [
                        'placeholder' => Yii::t('transportLogistics/forms', 'Select route'),
                    ],
                ],
            ],
             'price_invoice_with_vat:currency',
            [
                'attribute' => 'rule_type',
                'value' => function ($model) {
                    return $model->getRuleType();
                },
                'filter' => $searchModel::getRuleTypeArray(),
            ],
            [
                'attribute' => 'status',
                'value' => function ($model) {
                    return $model->getStatus();
                },
                'filter' => $searchModel::getStatusArray(),
            ],

            ['class' => 'yii\grid\ActionColumn',
                'template'=>'{view}',
                'buttons'=>[
                    'view'=> function ($url, $model, $key) {
                        return Html::a(Yii::t('buttons','View'), $url,['class'=>'btn btn-primary btn-grid-action-column']).'<br />';
                    },
                ]
            ],
        ],
    ]); ?>
</div>
<div>
   <?= Html::tag('span',Yii::t('transportLogistics/buttons','Export to Exel'),['class' => 'btn btn-success','id'=>'report-order-export-btn', 'data-url'=>'/warehouseDistribution/tupperware/billing/export-to-excel']) ?>
   <?= Html::tag('span',Yii::t('transportLogistics/buttons','Export to Exсel with condition'),['class' => 'btn btn-success','id'=>'report-order-if-export-btn', 'data-url'=>'/warehouseDistribution/tupperware/billing/export-to-excel']) ?>
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
