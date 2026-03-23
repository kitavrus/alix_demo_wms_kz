<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\billing\models\TlDeliveryProposalBilling */

$this->title = Yii::t('titles', 'Update Billing') . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('titles', 'Billings'), 'url' => ['index', 'tariffType'=>$model->tariff_type]];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('forms', 'Update');
?>
<div class="tl-delivery-proposal-billing-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
