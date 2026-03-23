<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use common\modules\transportLogistics\components\TLHelper;

/* @var $this yii\web\View */

$this->title = Yii::t('return/titles', 'Return order №').$model->order_number;
$this->params['breadcrumbs'][] = ['label' => Yii::t('return/titles', 'Report: return orders'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="report-order-view">

    <h1><?= Html::encode($this->title) ?></h1>


    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
           'id',
           'order_number',
            [
                'attribute' => 'status',
                'value' => $model->getStatusValue(),
            ],
            'expected_qty',
            'accepted_qty',
            'begin_datetime:datetime',
            'end_datetime:datetime',
            'created_at:datetime',
        ],
    ]) ?>

</div>

<h1 id="title-cars">
    <?= Html::encode(Yii::t('outbound/titles','Order items')) ?>
</h1>


<?=
GridView::widget([
    'dataProvider' => $ItemsProvider,
    'columns' => [
        'product_barcode',
        'product_model',
        'expected_qty',
        'accepted_qty',
        'status',
    ],
]);
?>