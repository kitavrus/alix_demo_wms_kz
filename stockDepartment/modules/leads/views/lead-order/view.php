<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\modules\leads\models\TransportationOrderLead;

/* @var $this yii\web\View */
/* @var $model common\modules\leads\models\TransportationOrderLead */

$this->title = Yii::t('leads/titles', 'Order №') . $model->order_number;
$this->params['breadcrumbs'][] = ['label' => Yii::t('leads/titles', 'Transportation Order Leads'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="transportation-order-lead-view col-md-8">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('buttons', 'Edit'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= is_object($model->externalClient) ? Html::a(Yii::t('buttons', 'Client profile'), ['lead-client/view', 'id' => $model->client_id], ['class' => 'btn btn-info']) : '' ?>
        <?= is_object($model->deliveryProposal) ? Html::a(Yii::t('buttons', 'Delivery proposal'), ['/tms/default/view', 'id' => $model->deliveryProposal->id], ['class' => 'btn btn-success']) : '' ?>
        <?= $model->status == TransportationOrderLead::STATUS_WAIT_FOR_CONFIRM ? Html::a(Yii::t('buttons', 'Confirm order'), ['confirm-lead-order', 'id' => $model->id], ['class' => 'btn btn-warning',  'data' => [
            'confirm' => Yii::t('titles', 'Are you sure you want to confirm this order?'),
            'method' => 'post',
        ],]) : '' ?>
        <?= Html::a(Yii::t('buttons', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger pull-right',
            'data' => [
                'confirm' => Yii::t('titles', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'order_number',
            [
                'attribute' => 'status',
                'value' => $model->getStatusValue(),
            ],
            [
                'attribute' => 'source',
                'value' => $model->getSourceValue(),
            ],
            'customer_name',
            'customer_phone',
            'customer_street',
            'customer_house',
            'customer_floor',
            'customer_apartment',
            [
                'label' => Yii::t('client/forms', 'City from'),
                'value' => is_object($model->fromCity) ? $model->fromCity->name : Yii::t('titles', 'Not set'),
            ],
            [
                'label' => Yii::t('client/forms', 'City to'),
                'value' => is_object($model->toCity) ? $model->toCity->name : Yii::t('titles', 'Not set'),
            ],
            'recipient_name',
            'recipient_phone',
            'recipient_street',
            'recipient_house',
            'recipient_floor',
            'recipient_apartment',
            'places',
            'customer_comment',
            'weight',
            'volume',
            'cost:currency',
            'cost_vat:currency',
            'declared_value',
            'package_description',
            'order_number',
            [
                'label' => Yii::t('forms', 'Created User ID'),
                'value' => is_object($model->createdUser) ? $model->createdUser->username : Yii::t('titles', 'Not set'),
            ],
            [
                'label' => Yii::t('forms', 'Updated User ID'),
                'value' => is_object($model->updatedUser) ? $model->updatedUser->username : Yii::t('titles', 'Not set'),
            ],
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

</div>
