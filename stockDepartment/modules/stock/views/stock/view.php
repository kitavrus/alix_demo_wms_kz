<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\modules\stock\models\Stock */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('froms', 'Stocks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="stock-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('froms', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('froms', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('froms', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'inbound_order_id',
            'outbound_order_id',
            'warehouse_id',
            'product_id',
            'product_name',
            'product_barcode',
            'product_model',
            'product_sku',
            'box_barcode',
            'condition_type',
            'status',
            'stock_availability',
            'primary_address',
            'secondary_address',
            'created_user_id',
            'updated_user_id',
            'created_at',
            'updated_at',
            'deleted',
        ],
    ]) ?>

</div>
