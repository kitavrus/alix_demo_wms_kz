<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\inbound\models\InboundOrder */

$this->title = Yii::t('forms', 'Update {modelClass}: ', [
    'modelClass' => 'Inbound Order',
]) . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('forms', 'Inbound Orders'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('forms', 'Update');
?>
<div class="inbound-order-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
