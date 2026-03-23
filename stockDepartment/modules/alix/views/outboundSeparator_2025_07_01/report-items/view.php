<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model stockDepartment\modules\alix\controllers\outboundSeparator\domain\entities\OutboundSeparatorItems */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Outbound Separator Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="outbound-separator-items-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'outbound_separator_id',
            'outbound_id',
            'order_number',
            'outbound_box_barcode',
            'product_barcode',
            'status',
            'created_user_id',
            'updated_user_id',
            'created_at',
            'updated_at',
            'deleted',
        ],
    ]) ?>

</div>
