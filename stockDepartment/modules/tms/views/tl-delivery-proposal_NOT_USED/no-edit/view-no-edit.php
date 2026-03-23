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

/* @var $this yii\web\View */
/* @var $model common\modules\transportLogistics\models\TlDeliveryProposal */
/* @var $dataProviderProposalRoutes common\modules\transportLogistics\models\TlDeliveryRoutes */
/* @var $dataProviderProposalOrders yii\data\ActiveDataProvider */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('transportLogistics/titles', 'Tl Delivery Proposals'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
    <div class="tl-delivery-proposal-view">

        <h1><?= Html::encode(Yii::t('transportLogistics/titles', 'Delivery proposal')) . '   № ' . $model->id ?> <span class="label label-danger"><?= $model::getStatusArray($model->status) ?></span></h1>

        <p>
            <?= Audit::haveAuditOrNot($model->id, 'TlDeliveryProposal') ? Html::a(Yii::t('titles', 'Show changelog'), ['/audit/default/index', 'parent_id' => $model->id, 'classname' => 'TlDeliveryProposal'], ['class' => 'btn btn-info']) : '' ?>
        </p>

        <?=
        DetailView::widget([
            'model' => $model,
            'attributes' => [
                [
                    'attribute'=>'client_id',
                    'type' => DetailView::INPUT_SELECT2,
                    'widgetOptions'=>[
                        'data'=>ArrayHelper::map(Client::findAll(['status' => Client::STATUS_ACTIVE]), 'id', 'title'),
                    ],
                    'value' => is_object($model->client) ? $model->client->title : Yii::t('titles', 'Not set'),

                ],
                [
                    'type' => DetailView::INPUT_DROPDOWN_LIST,
                    'items'=> $model::getCompanyTransporterArray(),
                    'attribute' => 'company_transporter',
                    'value' => $model->getCompanyTransporterValue(),

                ],
                'seal',
                [
                    'type' => DetailView::INPUT_DROPDOWN_LIST,
                    'items'=> $model::getDeliveryTypeArray(),
                    'attribute' => 'delivery_type',
                    'value' => $model->getDeliveryTypeValue(),

                ],
                [
                    'type' => DetailView::INPUT_DROPDOWN_LIST,
                    'items'=> $model::getDeliveryMethodArray(),
                    'attribute' => 'delivery_method',
                    'value' => $model->getDeliveryMethod(),

                ],
                [
                    'attribute' => 'route_from',
                    'type' => DetailView::INPUT_SELECT2,
                    'widgetOptions'=>[
                        'data'=>TLHelper::getStockPointArray(),
                    ],
                    'value' => is_string($model::getRouteFromTo($model->route_from)) ? $model::getRouteFromTo($model->route_from) : Yii::t('titles', 'Not set') ,
                ],
                [
                    'type' => DetailView::INPUT_SELECT2,
                    'widgetOptions'=>[
                        'data'=>TLHelper::getStockPointArray(),
                    ],
                    'attribute' => 'route_to',
                    'value' =>is_string($model::getRouteFromTo($model->route_to)) ? $model::getRouteFromTo($model->route_to) : Yii::t('titles', 'Not set') ,
                ],
                [
                    'attribute'=>'delivery_date',
                    'displayOnly' => (!empty($model->delivery_date) ? true : false),
                    'format' => 'datetime',
                ],
                [
                    'attribute'=>'expected_delivery_date',
                    'format' => 'datetime',
                ],
                [
                    'attribute'=>'accepted_datetime',
                    'format' => 'datetime',
                ],
                [
                    'attribute'=>'shipped_datetime',
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
                'number_places',
                'number_places_actual',

                [
                    'displayOnly' => true,
                    'type' => DetailView::INPUT_DROPDOWN_LIST,
                    'items'=> TlAgents::getActiveAgentsArray(),
                    'attribute' => 'agent_id',
                    'value' => TlAgents::getAgentValue($model->agent_id),

                ],
                [
                    'displayOnly' => true,
                    'type' => DetailView::INPUT_DROPDOWN_LIST,
                    'items'=> TlCars::getCarArray(),
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
                'price_our_profit:currency',
                [
                    'type' => DetailView::INPUT_DROPDOWN_LIST,
                    'items'=> $model::getPaymentMethodArray(),
                    'attribute' => 'cash_no',
                    'value' => $model->getPaymentMethodValue(),

                ],
                [
                    'displayOnly' => true,
                    'type' => DetailView::INPUT_DROPDOWN_LIST,
                    'items'=>$model::getStatusArray(),
                    'attribute' => 'status',
                    'value' => $model::getStatusArray($model->status),

                ],
                [
                    'type' => DetailView::INPUT_DROPDOWN_LIST,
                    'items'=> $model::getInvoiceStatusArray(),
                    'attribute' => 'status_invoice',
                    'value' => $model::getInvoiceStatusArray($model->status_invoice),

                ],
                [
                    'type' => DetailView::INPUT_DROPDOWN_LIST,
                    'items'=> $model::getSourceArray(),
                    'attribute' => 'source',
                    'value' => $model->getSourceValue(),
//                    'value' => $model::getSourceArray($model->source),

                ],
                [
                    'type' => DetailView::INPUT_DROPDOWN_LIST,
                    'items'=> $model::getNoChangePriceArray(),
                    'attribute' => 'change_price',
                    'value' => $model->getNoChangePriceValue(),

                ],
                [
                    'type' => DetailView::INPUT_DROPDOWN_LIST,
                    'items'=> $model::getNoChangeMcKgNpArray(),
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
    </h2>
<?php $dpID = $model->id;?>

<?=
GridView::widget([
    'dataProvider' => $dataProviderProposalOrders,
    'afterRow' => function ($model, $key, $index, $grid) {
        return $this->render('_view_dp_orders_extra-no-edit',['model'=>$model]);
    },
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        'order_number',
        [

            'attribute' => 'order_type',
            'value' => function ($model) {
                return TlDeliveryProposalOrders::getOrderTypeValue($model->order_type);
            },

        ],
        [

            'attribute' => 'delivery_type',
            'value' => function ($model) {
                return TlDeliveryProposalOrders::getDeliveryTypeValue($model->delivery_type);
            },

        ],
        'title',
        'description',
        'order_id',
    ],
]); ?>


    <h2 id="title-route">
        <?= Html::encode(Yii::t('transportLogistics/titles', 'Routes')) ?>
    </h2>

<div id="proposal-routes">
    <?= $this->render('_delivery_routes_grid-no-edit', [
        'dataProviderProposalRoutes' => $dataProviderProposalRoutes,
    ])?>
</div>

    <iframe style="display: none" name="print-ttn-frame" src="#" width="468" height="468">
    </iframe>
<?php
$row = '<table class="table table-striped table-bordered">';
if($model->price_expenses_cache){
    $row .= '<tr>';
    $row .= '<td style="width: 250px;"><strong>' . Yii::t('transportLogistics/buttons', 'Расходы наличные Итого: '). '</strong></td>';
    $row .= '<td >';
    $row .= '' . Yii::$app->formatter->asCurrency($model->price_expenses_cache);
    $row .= '</td>';
    $row .= '</tr>';
}

if($model->price_expenses_with_vat){
    $row .= '<tr>';
    $row .= '<td><strong>' . Yii::t('transportLogistics/buttons', 'Расходы с НДС Итого: '). '</strong></td>';
    $row .= '<td >';
    $row .= '' . Yii::$app->formatter->asCurrency($model->price_expenses_with_vat);
    $row .= '</td>';
    $row .= '</tr>';
}

if($model->price_expenses_total){
    $row .= '<tr>';
    $row .= '<td><strong>' . Yii::t('transportLogistics/buttons', 'Расходы Итого: '). '</strong></td>';
    $row .= '<td >';
    $row .= '' . Yii::$app->formatter->asCurrency($model->price_expenses_total);
    $row .= '</td>';
    $row .= '</tr>';
}

if($model->price_invoice){
    $row .= '<tr>';
    $row .= '<td><strong>' . Yii::t('transportLogistics/buttons', 'Продали клиенту за : '). '</strong></td>';
    $row .= '<td >';
    $row .= '' . Yii::$app->formatter->asCurrency($model->price_invoice);
    $row .= '</td>';
    $row .= '</tr>';
}

if($model->price_invoice_with_vat){
    $row .= '<tr>';
    $row .= '<td><strong>' . Yii::t('transportLogistics/buttons', 'Продали клиенту с НДС за : '). '</strong></td>';
    $row .= '<td >';
    $row .= '' . Yii::$app->formatter->asCurrency($model->price_invoice_with_vat);
    $row .= '</td>';
    $row .= '</tr>';
}

if($model->price_our_profit){
    $row .= '<tr>';
    $row .= '<td><strong>' . Yii::t('transportLogistics/buttons', 'Наша прибыль Итого: '). '</strong></td>';
    $row .= '<td >';
    $row .= '' . Yii::$app->formatter->asCurrency($model->price_our_profit);
    $row .= '</td>';
    $row .= '</tr>';
}


echo $row .= '</table>';
?>