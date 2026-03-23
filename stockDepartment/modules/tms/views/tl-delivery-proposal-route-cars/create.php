<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\modules\transportLogistics\models\TlDeliveryProposalRouteCars */

$this->title = Yii::t('forms', 'Create {modelClass}', [
    'modelClass' => 'Tl Delivery Proposal Route Cars',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('forms', 'Tl Delivery Proposal Route Cars'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tl-delivery-proposal-route-cars-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
