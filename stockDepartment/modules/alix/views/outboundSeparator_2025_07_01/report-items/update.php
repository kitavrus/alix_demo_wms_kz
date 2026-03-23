<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model stockDepartment\modules\intermode\controllers\outboundSeparator\domain\entities\OutboundSeparatorItems */

$this->title = 'Update Outbound Separator Items: {nameAttribute}';
$this->params['breadcrumbs'][] = ['label' => 'Outbound Separator Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="outbound-separator-items-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
