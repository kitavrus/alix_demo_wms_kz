<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\modules\billing\models\TlDeliveryProposalBilling */

$this->title = Yii::t('titles', 'Create billing');
$this->params['breadcrumbs'][] = ['label' => Yii::t('titles', 'Billings'), 'url' => ['index', 'tariffType'=>$model->tariff_type]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tl-delivery-proposal-billing-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
