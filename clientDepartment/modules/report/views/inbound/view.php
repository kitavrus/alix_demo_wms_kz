<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\grid\GridView;
use common\modules\transportLogistics\components\TLHelper;
use common\helpers\iHelper;

/* @var $this yii\web\View */
/* @var $model common\modules\billing\models\TlDeliveryProposalBilling */

$this->title = Yii::t('inbound/titles', 'Inbound order №').$model->order_number;
$this->params['breadcrumbs'][] = ['label' => Yii::t('inbound/titles', 'Report: inbound orders'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="report-order-view">

    <h1><?= Html::encode($this->title) ?></h1>
    <?= Html::a(Yii::t('transportLogistics/buttons','Export to Excel'),Url::to(['export-to-excel-full-one','id'=>$model->id]),['class' => 'btn btn-success','id'=>'report-order-export-plus-product-btn', 'data-url'=>'/report/outbound/export-to-excel-plus-product']) ?>
	 <?= Html::a(Yii::t('transportLogistics/buttons','Update comments'),Url::to(['comments-form','id'=>$model->id]),['class' => 'btn btn-info','id'=>'add-btn']) ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
           'id',
           'order_number',
           'comments',
            [
                'attribute' => 'order_type',
                'value' => $model->getOrderTypeValue(),
            ],
            [
                'attribute' => 'status',
                'value' => $model->getStatusValue(),
            ],
            'expected_qty',
            'accepted_qty',
            'expected_number_places_qty',
            'accepted_number_places_qty',
            'expected_datetime:datetime',
            'begin_datetime:datetime',
            'date_confirm:datetime',
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
    'filterModel' => $searchModel,
    'columns' => [
        'product_barcode',
        'product_model',
        'product_sku',
        'product_name',
        'expected_qty',
        'accepted_qty',

    ],
]);
?>