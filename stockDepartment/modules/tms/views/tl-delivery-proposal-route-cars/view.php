<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\modules\transportLogistics\transportLogistics;
use common\modules\audit\models\Audit;

/* @var $this yii\web\View */
/* @var $model common\modules\transportLogistics\models\TlDeliveryProposalRouteCars */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('transportLogistics/titles', 'Tl Delivery Proposal Route Cars'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tl-delivery-proposal-route-cars-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('transportLogistics/buttons', 'Edit'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('transportLogistics/buttons', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' =>Yii::t('transportLogistics/forms', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
        <?= Audit::haveAuditOrNot($model->id, 'TlDeliveryProposalRouteCars') ? Html::a(Yii::t('titles', 'Show changelog'), ['/audit/default/index', 'parent_id' => $model->id, 'classname' => 'TlDeliveryProposalRouteCars'], ['class' => 'btn btn-info']) : '' ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'route_city_from',
            'route_city_to',
            'delivery_date',
            'driver_name',
            'driver_phone',
            'driver_auto_number',
            'mc_filled',
            'kg_filled',
            'agent_id',
            'car_id',
            'grzch',
            'cash_no',
            'price_invoice',
            'price_invoice_with_vat',
            'status',
            'status_invoice',
            'comment:ntext',
            'created_user_id',
            'updated_user_id',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
