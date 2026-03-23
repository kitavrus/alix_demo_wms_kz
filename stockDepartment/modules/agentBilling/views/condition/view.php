<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\modules\audit\models\Audit;

/* @var $this yii\web\View */
/* @var $model common\modules\billing\models\TlDeliveryProposalBillingConditions */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('forms', 'Tl Delivery Proposal Billing Conditions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tl-delivery-proposal-billing-conditions-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('forms', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('forms', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('forms', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
        <?= Audit::haveAuditOrNot($model->id, 'TlDeliveryProposalBillingConditions') ? Html::a(Yii::t('titles', 'Show changelog'), ['/audit/default/index', 'parent_id' => $model->id, 'classname' => 'TlDeliveryProposalBillingConditions'], ['class' => 'btn btn-info']) : '' ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'tl_delivery_proposal_billing_id',
            'agent.title',
            'price_kg:currency',
            'price_kg_with_vat:currency',
            'price_mc:currency',
            'price_mc_with_vat:currency',
            'price_invoice:currency',
            'price_invoice_with_vat:currency',
            'formula_tariff:ntext',
            'status',
            'comment:ntext',
            'created_user_id',
            'updated_user_id',
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

</div>
