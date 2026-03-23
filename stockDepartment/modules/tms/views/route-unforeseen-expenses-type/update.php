<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\transportLogistics\models\TlDeliveryProposalRouteUnforeseenExpensesType */

$this->title = Yii::t('transportLogistics/forms', 'create-route-unforeseen-expenses-type');
$this->params['breadcrumbs'][] = ['label' => Yii::t('titles', 'Route unforeseen expenses type'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['breadcrumbs'][] = Yii::t('transportLogistics/forms', 'Update');
?>
<div class="tl-delivery-proposal-route-unforeseen-expenses-type-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
