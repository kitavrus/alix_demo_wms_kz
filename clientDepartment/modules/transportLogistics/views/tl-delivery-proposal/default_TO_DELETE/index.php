<?php

use yii\helpers\Html;
//use yii\grid\GridView;
use common\modules\transportLogistics\models\TlDeliveryProposal;
use app\modules\transportLogistics\transportLogistics;
use common\modules\transportLogistics\components\TLHelper;
use kartik\grid\DataColumn;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel clientDepartment\modules\transportLogistics\models\TlDeliveryProposalSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('transportLogistics/titles', 'Tl Delivery Proposals');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tl-delivery-proposal-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('transportLogistics/buttons', 'Create Tl Delivery Proposal' ), ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a(Yii::t('transportLogistics/buttons', 'Очистить поиск'), ['index'], ['class' => 'btn btn-primary','style'=>'float:right; margin-left:10px;']) ?>
        <?php if($isCountConfirm = TlDeliveryProposal::getCountIsWaitingConfirm()) { ?>
            <?= Html::a(Yii::t('transportLogistics/buttons', 'Ждут подтверждения').'  '.Html::tag('span',$isCountConfirm,['class'=>'label label-danger']).'', ['index','TlDeliveryProposalSearch[is_client_confirmed]'=>TlDeliveryProposal::IS_CLIENT_CONFIRMED_WAITING], ['class' => 'btn btn-warning','style'=>'float:right;']) ?>
        <?php } ?>
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
                    return isset ($value[$model->routeTo->id]) ? $value[$model->routeTo->id]:$model->routeTo->name;
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
            'delivery_date',
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
//            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
