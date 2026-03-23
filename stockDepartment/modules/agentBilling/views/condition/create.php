<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\modules\billing\models\TlDeliveryProposalBillingConditions */

$this->title = Yii::t('titles', 'Create billing condition', [
    'modelClass' => 'Tl Delivery Proposal Billing Conditions',
]);
//$this->params['breadcrumbs'][] = ['label' => Yii::t('titles', 'Billing conditions'), 'url' => ['index']];
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tl-delivery-proposal-billing-conditions-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'billing' => $billing,
    ]) ?>

</div>
