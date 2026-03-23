<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\modules\product\models\Product */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('titles', 'Products'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('buttons', 'Edit'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
<!--        --><?//= Html::a(Yii::t('buttons', 'Delete'), ['delete', 'id' => $model->id], [
//            'class' => 'btn btn-danger',
//            'data' => [
//                'confirm' => Yii::t('titles', 'Are you sure you want to delete this item?'),
//                'method' => 'post',
//            ],
//        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'model',
            'color',
            'size',
            'category',
            'gender',
            'field_extra1',
            'field_extra2',
            [
                'attribute' => 'status',
                'value' => $model->getStatus()
            ],
            'created_user_id',
            'updated_user_id',
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

</div>

<h1 id="title-cars">
	<?= Html::encode(Yii::t('outbound/titles','Product barcodes')) ?>
</h1>
<p>
	<?= Html::a(Yii::t('buttons', 'Create Product Barcode'), ['create-barcode',"product_id"=>$model->id], ['class' => 'btn btn-success']) ?>
</p>
<?=
GridView::widget([
	'dataProvider' => $itemsProvider,
	'filterModel' => $itemSearch,
	'columns' => [
		'barcode',
		['class' => 'yii\grid\ActionColumn',
			'template'=>'{update}',
			'buttons'=>[
//				'delete'=> function ($url, $model, $key) {
//					return   Html::a(Yii::t('buttons', 'Delete'), $url, [
//						'class' => 'btn btn-danger',
//						'data' => [
//							'confirm' => Yii::t('titles', 'Are you sure you want to delete this item?'),
//							'method' => 'post',
//						],
//					]);
//				},

				'update'=> function ($url, $model, $key) {
//	\yii\helpers\VarDumper::dump($url,10,true);
//	\yii\helpers\VarDumper::dump($model,10,true);
//	\yii\helpers\VarDumper::dump($key,10,true);
					return  Html::a(Yii::t('buttons', 'Edit'), ["update-barcode","id"=>$model->id],['class'=>'btn btn-primary']);
				},
//
//				'changelog'=> function ($url, $model, $key) {
//					return  Audit::haveAuditOrNot($model->id, 'TlDeliveryProposalBillingConditions') ? Html::a(Yii::t('titles', 'Show changelog'), ['/audit/default/index', 'parent_id' => $model->id, 'classname' => 'TlDeliveryProposalBillingConditions'], ['class' => 'btn btn-info']) : '';
//				},
			]
		]
	],
]);
?>
