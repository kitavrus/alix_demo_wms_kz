<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model stockDepartment\modules\transportLogistics\models\TlDeliveryProposalRouteCars */

$this->title = Yii::t('forms', 'Update {modelClass}: ', [
    'modelClass' => 'Tl Delivery Proposal Route Cars',
]) . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('forms', 'Tl Delivery Proposal Route Cars'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('forms', 'Update');
?>
<div class="tl-delivery-proposal-route-cars-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
