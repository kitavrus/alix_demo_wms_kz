<?php

use yii\helpers\Html;
//use yii\grid\GridView;
use common\modules\transportLogistics\models\TlDeliveryProposal;
use app\modules\transportLogistics\transportLogistics;
use common\modules\transportLogistics\components\TLHelper;
use common\modules\client\models\ClientEmployees;
use clientDepartment\modules\client\components\ClientManager;
use kartik\grid\DataColumn;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel clientDepartment\modules\transportLogistics\models\TlDeliveryProposalSearch */
/* @var $filterWidgetOptionDataRoute common\modules\store\models\Store */
/* @var $searchModel common\modules\store\models\Store */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('transportLogistics/titles', 'Tl Delivery Proposals');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tl-delivery-proposal-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('titles', 'Clear search'), ['index'], ['class' => 'btn btn-primary','style'=>'float:right; margin-left:10px;margin-top: -50px;']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute'=> 'id',
//                'options' => ['width'=>'20px'],

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
            [
                'class' => DataColumn::className(),
                'attribute' => 'shipped_datetime',
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
                'class' => DataColumn::className(),
                'attribute' => 'expected_delivery_date',
                'filterType' => GridView::FILTER_DATE_RANGE,
                'filterWidgetOptions' => [
                    'convertFormat'=>true,
                    'pluginOptions'=>[
                        'locale'=>[
                            'separator'=> ' / ',
                            'format'=>'Y-m-d',
                        ]
//                        'separator'=> ' / ',
//                        'timePicker'=>false,
//                        'format'=>'Y-m-d'
                    ]
                ],
            ],
            [
                'class' => DataColumn::className(),
                'attribute' => 'delivery_date',
                'filterType' => GridView::FILTER_DATE_RANGE,
                'filterWidgetOptions' => [
                    'convertFormat'=>true,
                    'pluginOptions'=>[
                        'locale'=>[
                            'separator'=> ' / ',
                            'format'=>'Y-m-d',
                        ]
                    ]
                ],
            ],
            'mc',
            'number_places',
            'price_invoice_with_vat:currency',
             [
                 'attribute'=> 'status',
                 'filter'=> $searchModel::getStatusArray(),
                 'value' => function ($data) {return TlDeliveryProposal::getStatusArray($data->status);},

             ],
        ],
    ]); ?>

</div>

<script type="text/javascript">
    $(function(){
        $('#tldeliveryproposalsearch-shipped_datetime').on('apply.daterangepicker', function(ev, picker) {
            $(this).trigger('change');
        });

        $('#tldeliveryproposalsearch-expected_delivery_date').on('apply.daterangepicker', function(ev, picker) {
            $(this).trigger('change');
        });

        $('#tldeliveryproposalsearch-delivery_date').on('apply.daterangepicker', function(ev, picker) {
            $(this).trigger('change');
        });
    });
</script>
