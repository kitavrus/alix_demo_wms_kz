<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use common\modules\transportLogistics\models\TlDeliveryProposalOrders;
use common\modules\transportLogistics\models\TlDeliveryProposal;
use common\modules\transportLogistics\models\TlDeliveryRoutes;
use common\modules\transportLogistics\models\TlAgents;
use common\modules\transportLogistics\models\TlCars;
use app\modules\transportLogistics\transportLogistics;
use kartik\detail\DetailView;
use kartik\grid\EditableColumn;
use common\modules\client\models\Client;
use common\modules\transportLogistics\components\TLHelper;
use common\modules\audit\models\Audit;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $model common\modules\transportLogistics\models\TlDeliveryProposal */
/* @var $dataProviderProposalRoutes common\modules\transportLogistics\models\TlDeliveryRoutes */
/* @var $dataProviderProposalOrders yii\data\ActiveDataProvider */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('transportLogistics/titles', 'Tl Delivery Proposals'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tl-delivery-proposal-view">

    <h1><?= Html::encode(Yii::t('transportLogistics/titles', 'Delivery proposal')) . '   № ' . $model->id ?> <span
            class="label label-danger"><?= $model::getStatusArray($model->status) ?></span>
<!--        <span class="label label-warning" id="ready-invoice-title">--><?php //= $model->getReadyToInvoicingValue() ?><!--</span>-->
    </h1>

    <p>
        <?= Html::a(Yii::t('transportLogistics/buttons', 'Edit'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>

        <?php if ($model->canPrintTtn()) {
            echo Html::tag('span', Yii::t('titles', 'Print TTN'), ['class' => 'btn btn-info', 'id' => 'print-ttn-btn', 'data-printtype' => Yii::$app->params['printType'], 'data-href' => 'print-ttn?id=' . $model->id]);
            echo "&nbsp;&nbsp;&nbsp;".Html::a(Yii::t('transportLogistics/buttons', 'Распечатать этикетки на короба'), ['print-box-label', 'id' => $model->id], ['class' => 'btn btn-primary']);
//            echo "&nbsp;&nbsp;&nbsp;".Html::tag('span', Yii::t('titles', 'Print box label'), ['class' => 'btn btn-warning', 'id' => 'print-ttn-btn', 'data-printtype' => Yii::$app->params['printType'], 'data-href' => 'print-box-label?id=' . $model->id]);
        } ?>

        <?php  echo Html::a(Yii::t('transportLogistics/buttons', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'style' => 'float:right; display:none;',
            'data' => [
                'confirm' => Yii::t('transportLogistics/forms', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>

        <?= Audit::haveAuditOrNot($model->id, 'TlDeliveryProposal') ? Html::a(Yii::t('titles', 'Show changelog'), ['/audit/default/index', 'parent_id' => $model->id, 'classname' => 'TlDeliveryProposal'], ['class' => 'btn btn-info']) : '' ?>

        <?php if (!$routes = $model->proposalRoutes) {
            echo Html::tag('span', Yii::t('transportLogistics/buttons', 'Form Route'), [
                'class' => 'btn btn-warning',
                'style' => 'margin-left: 10px;',
                'id' => 'form-route-bt',
                'data' => [
                    'url' => Url::toRoute(['check-default-route', 'id' => $model->id]),
                    'url-save' => Url::toRoute(['form-proposal-route']),
//                        'url' => Url::toRoute('form-proposal-route'),
                    'id' => $model->id,
                ],
            ]);
        }
        ?>
<!--        --><?php /*if ($model->ready_to_invoicing == TlDeliveryProposal::READY_TO_INVOICING_NO) {
            echo Html::tag('span', Yii::t('transportLogistics/buttons', 'Make ready to invoicing'), [
                'class' => 'btn btn-warning',
                'style' => 'margin-left: 10px;',
                'id' => 'make-ready-invoice-bt',
                'data' => [
                    'url' => Url::toRoute('make-ready-invoice'),
                    'id' => $model->id,
                ],
            ]);
        }
        */?>
        <!--        --><? //= Html::a(Yii::t('transportLogistics/buttons', 'Recalculate'), ['order-recalculate', 'id' => $model->id], ['class' => 'btn btn-primary', 'style' => 'float:right; ',]) ?>

    </p>

    <?=
    DetailView::widget([
        'model' => $model,
//            'panel' => [
//
//                'heading' => Html::encode(Yii::t('transportLogistics/titles', 'Delivery proposal')) . '   № ' . $model->id,
//                'type' => DetailView::TYPE_SUCCESS,
//            ],
        'attributes' => [
            [
//                    'visible'=>false,
                'attribute' => 'client_id',
                'type' => DetailView::INPUT_SELECT2,
                'widgetOptions' => [
                    'data' => ArrayHelper::map(Client::findAll(['status' => Client::STATUS_ACTIVE]), 'id', 'title'),
                ],
                'value' => is_object($model->client) ? $model->client->title : Yii::t('titles', 'Not set'),

            ],
            [
                'type' => DetailView::INPUT_DROPDOWN_LIST,
                'items' => $model::getCompanyTransporterArray(),
                'attribute' => 'company_transporter',
                'value' => $model->getCompanyTransporterValue(),

            ],
            'seal',
            [
                'type' => DetailView::INPUT_DROPDOWN_LIST,
                'items' => $model::getDeliveryTypeArray(),
                'attribute' => 'delivery_type',
                'value' => $model->getDeliveryTypeValue(),

            ],
            [
                'type' => DetailView::INPUT_DROPDOWN_LIST,
                'items' => $model::getDeliveryMethodArray(),
                'attribute' => 'delivery_method',
                'value' => $model->getDeliveryMethod(),

            ],
            [
                'attribute' => 'route_from',
                'type' => DetailView::INPUT_SELECT2,
                'widgetOptions' => [
                    'data' => TLHelper::getStockPointArray(),
                ],
//                'value' => is_string($model::getRouteFromTo($model->route_from)) ? $model::getRouteFromTo($model->route_from) : Yii::t('titles', 'Not set'),
                'value' => isset($storeArray[$model->route_from]) ? $storeArray[$model->route_from] : Yii::t('titles', 'Not set'),
            ],
            [
                'type' => DetailView::INPUT_SELECT2,
                'widgetOptions' => [
                    'data' => TLHelper::getStockPointArray(),
                ],
                'attribute' => 'route_to',
                'value' => isset($storeArray[$model->route_to]) ? $storeArray[$model->route_to] : Yii::t('titles', 'Not set'),
                //'value' => is_string($model::getRouteFromTo($model->route_to)) ? $model::getRouteFromTo($model->route_to) : Yii::t('titles', 'Not set'),
            ],
            [
                'attribute' => 'delivery_date',
                'displayOnly' => (!empty($model->delivery_date) ? true : false),
                'format' => 'datetime',
            ],
            [
                'attribute' => 'expected_delivery_date',
                'format' => 'datetime',
            ],
            [
                'attribute' => 'accepted_datetime',
                'format' => 'datetime',
            ],
            [
                'attribute' => 'shipped_datetime',
                'format' => 'datetime',
            ],
            'mc:decimal',
//            [
//              'attribute'=>'mc',
//              'format'=>'mc',
//            ],
            'mc_actual',
            'kg',
            'kg_actual',
            'volumetric_weight',
            'number_places',
            'number_places_actual',
            [
                'displayOnly' => true,
                'type' => DetailView::INPUT_DROPDOWN_LIST,
                'items' => TlAgents::getActiveAgentsArray(),
                'attribute' => 'agent_id',
                'value' => TlAgents::getAgentValue($model->agent_id),

            ],
            [
                'displayOnly' => true,
                'type' => DetailView::INPUT_DROPDOWN_LIST,
                'items' => TlCars::getCarArray(),
                'attribute' => 'car_id',
                'value' => TlCars::getCarValue($model->car_id),

            ],

            'driver_name',
            'driver_phone',
            'driver_auto_number',

            'price_invoice:currency',
            'price_invoice_with_vat:currency',


            [
                'displayOnly' => true,
                'attribute' => 'price_expenses_total',
                'format' => 'currency'

            ],

            [
                'displayOnly' => true,
                'attribute' => 'price_expenses_cache',
                'format' => 'currency'

            ],

            [
                'displayOnly' => true,
                'attribute' => 'price_expenses_with_vat',
                'format' => 'currency'

            ],
//                'price_our_profit:currency',
            [
                'type' => DetailView::INPUT_DROPDOWN_LIST,
                'items' => $model::getPaymentMethodArray(),
                'attribute' => 'cash_no',
                'value' => $model->getPaymentMethodValue(),

            ],
            [
                'displayOnly' => true,
                'type' => DetailView::INPUT_DROPDOWN_LIST,
                'items' => $model::getStatusArray(),
                'attribute' => 'status',
                'value' => $model::getStatusArray($model->status),

            ],
            [
                'type' => DetailView::INPUT_DROPDOWN_LIST,
                'items' => $model::getInvoiceStatusArray(),
                'attribute' => 'status_invoice',
                'value' => $model::getInvoiceStatusArray($model->status_invoice),

            ],
            [
                'type' => DetailView::INPUT_DROPDOWN_LIST,
                'items' => $model::getSourceArray(),
                'attribute' => 'source',
                'value' => $model->getSourceValue(),
//                    'value' => $model::getSourceArray($model->source),

            ],
            [
                'type' => DetailView::INPUT_DROPDOWN_LIST,
                'items' => $model::getNoChangePriceArray(),
                'attribute' => 'change_price',
                'value' => $model->getNoChangePriceValue(),

            ],
            [
                'type' => DetailView::INPUT_DROPDOWN_LIST,
                'items' => $model::getNoChangeMcKgNpArray(),
                'attribute' => 'change_mckgnp',
                'value' => $model->getNoChangeMcKgNpValue(),

            ],
            [
                'type' => DetailView::INPUT_TEXTAREA,
                'attribute' => 'comment',

            ],
            [
                'displayOnly' => true,
                'attribute' => 'created_user_id',
                'value' => $model::getUserName($model->created_user_id),

            ],
            [
                'displayOnly' => true,
                'attribute' => 'updated_user_id',
                'value' => $model::getUserName($model->updated_user_id),

            ],
            [
                'displayOnly' => true,
                'attribute' => 'created_at',
                'format' => 'datetime'

            ],
            [
                'displayOnly' => true,
                'attribute' => 'updated_at',
                'format' => 'datetime'

            ],
        ],
    ]) ?>
</div>

<h2 id="title-order">
    <?= Html::encode(Yii::t('transportLogistics/titles', 'Orders, that must delivered in this proposal')) ?>
    <?= Html::a(Html::tag('span', '', ['class' => 'glyphicon glyphicon-plus-sign']), ['add-order', 'proposal_id' => $model->id], ['class' => 'btn btn-primary', 'style' => 'float:right; ',]); // Yii::t('transportLogistics/buttons', 'Add order')  ?>
</h2>
<?php $dpID = $model->id; ?>

<?=
GridView::widget([
    'dataProvider' => $dataProviderProposalOrders,
    'afterRow' => function ($model, $key, $index, $grid) {
        return $this->render('_view_dp_orders_extra', ['model' => $model]);
    },
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        'order_number',
        [
            'class' => EditableColumn::className(),
            'editableOptions' => [
                'inputType' => 'dropDownList',
                'data' => TlDeliveryProposalOrders::getOrderTypeArray(),
            ],
            'attribute' => 'order_type',
            'value' => function ($model) {
                return TlDeliveryProposalOrders::getOrderTypeValue($model->order_type);
            },

        ],
        [
            'class' => EditableColumn::className(),
            'editableOptions' => [
                'inputType' => 'dropDownList',
                'data' => TlDeliveryProposalOrders::getDeliveryTypeArray(),
            ],
            'attribute' => 'delivery_type',
            'value' => function ($model) {
                return TlDeliveryProposalOrders::getDeliveryTypeValue($model->delivery_type);
            },

        ],
        'order_id',
        'title',
        'description',
//        [
//            'attribute' => 'client_id',
//            'value' => function ($model) {
//                return $model->client->title;
//            },
//        ],
        ['class' => 'yii\grid\ActionColumn',
            'template' => '{update} {delete} {copy}',
            'urlCreator' => function ($action, $model, $key, $index) {
                $params = ['id' => $model->id];
                $params[0] = $action . '-order';

                return Url::toRoute($params);
            },
            'buttons' => [
                'copy' => function ($url, $model, $key) use ($dpID) {
                    $bt = Html::a(Yii::t('transportLogistics/buttons', 'Copy order to new DP'), 'copy-order?proposal_order_id=' . $model->id . '&proposal_id=' . $dpID,
                        [
                            'class' => 'btn btn-danger',
                            'style' => ' margin-left:10px;',
                        ]);

                    return $bt;
                }
            ]
        ],
    ],
]); ?>


<h2 id="title-route">
    <?= Html::encode(Yii::t('transportLogistics/titles', 'Routes')) ?>
    <?= Html::a(Html::tag('span', '', ['class' => 'glyphicon glyphicon-plus-sign']), ['add-route', 'proposal_id' => $model->id], ['class' => 'btn btn-primary', 'style' => 'float:right; ',]); ?>
</h2>
<div id="proposal-routes">
    <?= $this->render('_delivery_routes_grid', [
        'dataProviderProposalRoutes' => $dataProviderProposalRoutes,
    ]) ?>
</div>
<p class="text-center">
    <?= Html::a(Yii::t('transportLogistics/forms', 'Create new Delivery Proposal'), ['create'], ['class' => 'btn btn-success']) ?>
    <?php if ($model->canPrintTtn()) { ?>
        <?= Html::a(Yii::t('titles', 'Print TTN'), ['print-ttn', 'id' => $model->id], ['class' => 'btn btn-info']) ?>
    <?php } ?>
</p>

<iframe style="display: none" name="print-ttn-frame" src="#" width="468" height="468">
</iframe>
<?php
$row = '<table class="table table-striped table-bordered">';
if ($model->price_expenses_cache) {
    $row .= '<tr>';
    $row .= '<td style="width: 250px;"><strong>' . Yii::t('transportLogistics/buttons', 'Расходы наличные Итого: ') . '</strong></td>';
    $row .= '<td >';
    $row .= '' . Yii::$app->formatter->asCurrency($model->price_expenses_cache);
    $row .= '</td>';
    $row .= '</tr>';
}

if ($model->price_expenses_with_vat) {
    $row .= '<tr>';
    $row .= '<td><strong>' . Yii::t('transportLogistics/buttons', 'Расходы с НДС Итого: ') . '</strong></td>';
    $row .= '<td >';
    $row .= '' . Yii::$app->formatter->asCurrency($model->price_expenses_with_vat);
    $row .= '</td>';
    $row .= '</tr>';
}

if ($model->price_expenses_total) {
    $row .= '<tr>';
    $row .= '<td><strong>' . Yii::t('transportLogistics/buttons', 'Расходы Итого: ') . '</strong></td>';
    $row .= '<td >';
    $row .= '' . Yii::$app->formatter->asCurrency($model->price_expenses_total);
    $row .= '</td>';
    $row .= '</tr>';
}

if ($model->price_invoice) {
    $row .= '<tr>';
    $row .= '<td><strong>' . Yii::t('transportLogistics/buttons', 'Продали клиенту за : ') . '</strong></td>';
    $row .= '<td >';
    $row .= '' . Yii::$app->formatter->asCurrency($model->price_invoice);
    $row .= '</td>';
    $row .= '</tr>';
}

if ($model->price_invoice_with_vat) {
    $row .= '<tr>';
    $row .= '<td><strong>' . Yii::t('transportLogistics/buttons', 'Продали клиенту с НДС за : ') . '</strong></td>';
    $row .= '<td >';
    $row .= '' . Yii::$app->formatter->asCurrency($model->price_invoice_with_vat);
    $row .= '</td>';
    $row .= '</tr>';
}

if ($model->price_our_profit) {
    $row .= '<tr>';
    $row .= '<td><strong>' . Yii::t('transportLogistics/buttons', 'Наша прибыль Итого: ') . '</strong></td>';
    $row .= '<td >';
    $row .= '' . Yii::$app->formatter->asCurrency($model->price_our_profit);
    $row .= '</td>';
    $row .= '</tr>';
}


//echo $row .= '</table>';
?>

<?php Modal::begin([
    'header' => '<h4 id="delivery-proposal-index-header"></h4>',
    'id' => 'delivery-proposal-index-modal'
]); ?>
<?= "<div id='delivery-proposal-index-errors'></div>"; ?>
<?= "<div id='delivery-proposal-index-content'></div>"; ?>
<?php Modal::end(); ?>

<script type="text/javascript">
    $(function () {

        var b = $('body');

        //S
        b.on('beforeSubmit', '#select-sub-route-model-popup-form', function (e) {
            console.info('#select-sub-route-model-popup-form on beforeSubmit');

            var form = $(this),
                me = $('#form-route-bt'),
                action = form.attr("action"),
                submitButton = $('#select-sub-route-submit-bt'),
                buttonText = submitButton.text(),
                serialize = form.serializeArray();

            submitButton.text('Подождите пожалуйста, идет обработка данных...');

            $.post(action, serialize, function (result) {
                HideClearPopup();
                submitButton.text(buttonText);
                var div = $('#proposal-routes');

                me.text('Маршруты формируются, пожалуйста подождите...');
                setTimeout(function () {
                    me.hide();
                    div.html(result.data);
                    $('html,body').stop().animate({scrollTop: div.offset().top}, 1000);
                });

            }, 'json').fail(function () {
                console.log("server error");
            });

            return false;
        });

        //S
        b.on('submit', '#select-sub-route-model-popup-form', function (e) {
            console.info('e.preventDefault on submit');
            e.preventDefault();
            return false;
        });

        //S
        b.on('change', '#selectsubroutedefault-sub_default_route_id', function (e) {
            console.info('-selectsubroutedefault-sub_default_route_id-');
            var url = $(this).data('url'),
                valueID = $(this).val();

            $.post(url, {'id': valueID}, function (data) {
                $('#default-sub-route-grid').html(data);
            });
        });

    });

    /*
     * Set content and show popup
     * @param string Content be should showed
     * */
    function ShowPopupContent(toShow) {
        $('#delivery-proposal-index-modal').
            modal('show')
            .find('#delivery-proposal-index-content')
            .html(toShow);
    }

    /*
     * Set errors in popup
     * @param string Content be should showed
     * */
    function ShowPopupErrors(toShow) {
        $('#delivery-proposal-index-modal')
            .find('#delivery-proposal-index-errors')
            .html(toShow);
    }

    /*
     * Hide and clear content in popup
     * */
    function HideClearPopup() {
        $('#delivery-proposal-index-modal').modal('hide');
        $('#delivery-proposal-index-errors').html('');
        $('#delivery-proposal-index-content').html('');
    }
</script>