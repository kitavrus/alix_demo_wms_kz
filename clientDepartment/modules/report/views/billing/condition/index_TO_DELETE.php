<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel stockDepartment\modules\billing\models\TlDeliveryProposalBillingConditionsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('forms', 'Tl Delivery Proposal Billing Conditions');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tl-delivery-proposal-billing-conditions-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('forms', 'Create {modelClass}', [
    'modelClass' => 'Tl Delivery Proposal Billing Conditions',
]), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'tl_delivery_proposal_billing_id',
            'client_id',
            'price_invoice',
            'price_invoice_with_vat',
            // 'formula_tariff:ntext',
            // 'status',
            // 'comment:ntext',
            // 'created_user_id',
            // 'updated_user_id',
            // 'created_at',
            // 'updated_at',
            // 'deleted',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
