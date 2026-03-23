<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\transportLogistics\models\TlDeliveryProposalDefaultRoute */

$this->title = Yii::t('transportLogistics/titles', 'Update Default Route № {0}', [$model->id]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('transportLogistics/titles', 'Tl Delivery Proposal Default Routes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('titles', 'Update');
?>
<div class="tl-delivery-proposal-default-route-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
