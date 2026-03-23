<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\ecommerce\entities\EcommerceCheckBoxInventorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Ecommerce Check Box Inventories');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ecommerce-check-box-inventory-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>
    <p>
        <?= Html::a(Yii::t('app', 'Создать Новую инвентори'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'description:text',
            [
                'attribute'=> 'inventory_key',
                'format'=> 'html',
                'value' => function ($data) { return Html::tag('a', $data->inventory_key, ['href'=>\yii\helpers\Url::to(['view', 'id' => $data->id]), 'target'=>'_blank']);},
            ],
            [
                'attribute'=> 'status',
                'format'=> 'html',
                'value' => function ($data) {
                    return \common\ecommerce\constants\CheckBoxStatus::getValue($data->status);
                },
            ],
            'expected_product_qty',
            'scanned_product_qty',
            'expected_box_qty',
            'scanned_box_qty',
            'begin_datetime:datetime',
            'end_datetime:datetime',
            'complete_date:datetime',
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]); ?>
</div>
