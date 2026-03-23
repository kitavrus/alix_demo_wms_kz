<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\modules\transportLogistics\models\TlDeliveryProposalOrderBoxes */

$this->title = Yii::t('app', 'Create Tl Delivery Proposal Order Boxes');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Tl Delivery Proposal Order Boxes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tl-delivery-proposal-order-boxes-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
