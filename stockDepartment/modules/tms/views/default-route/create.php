<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\modules\transportLogistics\models\TlDeliveryProposalDefaultRoute */

$this->title = Yii::t('transportLogistics/titles', 'Create Tl Delivery Proposal Default Route');
$this->params['breadcrumbs'][] = ['label' => Yii::t('transportLogistics/titles', 'Tl Delivery Proposal Default Routes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tl-delivery-proposal-default-route-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
