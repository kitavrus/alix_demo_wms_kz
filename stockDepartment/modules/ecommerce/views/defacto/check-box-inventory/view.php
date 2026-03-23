<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\ecommerce\entities\EcommerceCheckBoxInventory */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Ecommerce Check Box Inventories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ecommerce-check-box-inventory-view">
<!--    <h1>--><?//= Html::encode($this->title) ?><!--</h1>-->
    <p>
        <?= Html::a(Yii::t('app', 'Начать сканировать'), ['/ecommerce/defacto/check-box/scanning', 'inventoryId' => $model->id], ['class' => 'btn btn-info']) ?>
        <?= Html::a(Yii::t('app', 'Что внутри'), ['/ecommerce/defacto/check-box/index', 'EcommerceCheckBoxSearch[inventory_id]' => $model->id], ['class' => 'btn btn-primary']) ?>

        <?php if(!\common\ecommerce\constants\CheckBoxStatus::isDone($model->status)) { ?>
            <?= Html::a(Yii::t('app', 'Закрыть'), ['/ecommerce/defacto/check-box-inventory/complete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'style' => 'float:right; font-size: 25px; margin: -12px 5px 0px 5px'
            ]) ?>
        <?php } ?>

<!--        --><?//= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
//            'class' => 'btn btn-danger',
//            'data' => [
//                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
//                'method' => 'post',
//            ],
//        ]) ?>
    </p>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'description',
            'inventory_key',
            [
                'attribute'=> 'status',
                'format'=> 'html',
                'value' => function ($model) {
                    return \common\ecommerce\constants\CheckBoxStatus::getValue($model->status);
                },
            ],
            'expected_product_qty',
            'scanned_product_qty',
            'expected_box_qty',
            'scanned_box_qty',
            'begin_datetime:datetime',
            'end_datetime:datetime',
            'complete_date',
            'created_user_id',
            'updated_user_id',
            'created_at:datetime',
            'updated_at:datetime',
//            'deleted',
        ],
    ]) ?>
</div>