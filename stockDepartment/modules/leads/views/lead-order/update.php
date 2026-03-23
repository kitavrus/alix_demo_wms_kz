<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\leads\models\TransportationOrderLead */

$this->title = Yii::t('leads/titles', 'Update order №') . $model->order_number;
$this->params['breadcrumbs'][] = ['label' => Yii::t('leads/titles', 'Transportation Order Leads'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' =>Yii::t('leads/titles', 'Order №').$model->order_number, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('titles', 'Update');
?>
<div class="transportation-order-lead-update col-md-8">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
