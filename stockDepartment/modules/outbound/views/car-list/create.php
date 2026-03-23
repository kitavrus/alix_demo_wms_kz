<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\modules\transportLogistics\models\TlDeliveryProposalDefaultSubRoute */

$this->title = Yii::t('outbound/titles', 'Create Outbound Order');
?>
<div class="outbound-order-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'clientsArray'=>$clientsArray,
        'storeArray'=>$storeArray,
    ]) ?>

</div>
