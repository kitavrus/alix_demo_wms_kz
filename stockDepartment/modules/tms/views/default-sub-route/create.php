<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\modules\transportLogistics\models\TlDeliveryProposalDefaultSubRoute */

$this->title = Yii::t('transportLogistics/titles', 'Create Tl Delivery Proposal Default Sub Route');
?>
<div class="tl-delivery-proposal-default-sub-route-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
