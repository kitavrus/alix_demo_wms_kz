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
        'id' => 'delivery-proposal-grid-view',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute'=> 'id',
//                'options' => ['width'=>'20px'],
            ],
            [
                'attribute'=> 'orders',
                'value' => function ($data) { return $data->getExtraFieldValueByName('orders');},
            ],
            [
                'class' => DataColumn::className(),
                'attribute' => 'route_from',
                'value' => function ($model) use ($filterWidgetOptionDataRoute) {
//                    $value = TLHelper::getStoreArrayByClientID();
                    $value = $filterWidgetOptionDataRoute;
                    return isset ($value[$model->route_from]) ? $value[$model->route_from]:'-NONE-';
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
//                    $value = TLHelper::getStoreArrayByClientID();
                    $value = $filterWidgetOptionDataRoute;

                    return isset ($value[$model->route_to]) ? $value[$model->route_to]:'-NONE-';
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
                'format' => 'datetime',
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
                'format' => 'datetime',
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
                'attribute' => 'delivery_date',
                'format' => 'datetime',
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
            'mc',
            [
                'attribute'=> 'kg',
                'visible' => ClientManager::canViewAttribute($searchModel)

            ],
            'number_places',
//
//            [
//                'attribute'=> 'cash_no',
//                'filter'=> $searchModel->getPaymentMethodArray(),
//                'value' => function ($data) {return TlDeliveryProposal::getPaymentMethodArray($data->cash_no);},
//
//            ],
//            'price_invoice',
//            'price_invoice_with_vat',
             [
                 'attribute'=> 'status',
                 'filter'=> $searchModel::getStatusForClientArray(),
                 'value' => function ($data) {return $data->getStatusForClient();},
//                 'value' => function ($data) {return TlDeliveryProposal::getStatusArray($data->status);},

             ],
//            [
//                'attribute'=> 'status_invoice',
//                'filter'=> $searchModel::getInvoiceStatusArray(),
//                'value' => function ($data) {return TlDeliveryProposal::getInvoiceStatusArray($data->status_invoice);},
//
//            ],
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
//                    'delete'=> function ($url, $model, $key) {
//                        $button = '';
//                        if(ClientManager::canPrintTTNDeliveryProposal($model)) {
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
<?php if(Yii::$app->user->id == 57) { // Musa Demirok ?>
<?php echo  Html::tag('span',Yii::t('transportLogistics/buttons','Export to Exel'),['class' => 'btn btn-success','id'=>'delivery-proposal-export-btn']) ?>
<?php } ?>
<script type="text/javascript">
    $(function() {

        $('#delivery-proposal-export-btn').on('click',function() {
            var keys = window.location.href;
            console.info($('#delivery-proposal-grid-view-filters').find('.form-control').serialize());
            console.info(keys);
            window.location.href = '/transportLogistics/tl-delivery-proposal/export-to-excel?'+$('#delivery-proposal-grid-view-filters').find('.form-control').serialize();
        });

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