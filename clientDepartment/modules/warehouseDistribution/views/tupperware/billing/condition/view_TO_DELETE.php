<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

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
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'tl_delivery_proposal_billing_id',
            'client_id',
            'price_invoice',
            'price_invoice_with_vat',
            'formula_tariff:ntext',
            'status',
            'comment:ntext',
//            'created_user_id',
//            'updated_user_id',
//            'created_at:datetime',
//            'updated_at:datetime',
        ],
    ]) ?>

</div>
