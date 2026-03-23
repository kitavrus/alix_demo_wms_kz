<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model stockDepartment\modules\intermode\controllers\outboundSeparator\domain\entities\OutboundSeparator */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Outbound Separators', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="outbound-separator-view">
    <h1><?= Html::encode($this->title) ?></h1>
<!--    <p>-->
<!--        --><?//= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
<!--        --><?//= Html::a('Delete', ['delete', 'id' => $model->id], [
//            'class' => 'btn btn-danger',
//            'data' => [
//                'confirm' => 'Are you sure you want to delete this item?',
//                'method' => 'post',
//            ],
//        ]) ?>
<!--    </p>-->

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'order_number',
            'comments',
            'status',
            'path_to_file:ntext',
            'created_user_id',
            'updated_user_id',
            'created_at',
            'updated_at',
            'deleted',
        ],
    ]) ?>
</div>

<?=
GridView::widget([
	'dataProvider' => $ItemsProvider,
	'filterModel' => $searchModel,
	'columns' => [
		['class' => 'yii\grid\SerialColumn'],
		'order_number',
		'outbound_box_barcode',
		'outbound_box_barcode',
		'product_barcode',
		'status',
	],
]);
?>