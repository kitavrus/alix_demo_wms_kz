<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\modules\inbound\models\InboundOrder */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('forms', 'Inbound Orders'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="inbound-order-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('forms', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('forms', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('forms', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'client_id',
            'supplier_id',
            'warehouse_id',
            'order_number',
            'order_type',
            'status',
            'expected_qty',
            'accepted_qty',
            'accepted_number_places_qty',
            'expected_number_places_qty',
            'expected_datetime:datetime',
            'begin_datetime:datetime',
            'end_datetime:datetime',
            'created_user_id',
            'updated_user_id',
            'created_at',
            'updated_at',
            'deleted',
        ],
    ]) ?>

</div>
