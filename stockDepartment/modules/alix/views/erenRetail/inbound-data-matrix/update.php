<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\dataMatrix\models\InboundDataMatrix */

$this->title = 'Update Inbound Data Matrix: {nameAttribute}';
$this->params['breadcrumbs'][] = ['label' => 'Inbound Data Matrices', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="inbound-data-matrix-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
