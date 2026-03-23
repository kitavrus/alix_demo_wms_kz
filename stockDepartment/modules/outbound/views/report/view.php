<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\grid\GridView;
use common\modules\transportLogistics\components\TLHelper;
use common\helpers\iHelper;

/* @var $this yii\web\View */
/* @var $model common\modules\billing\models\TlDeliveryProposalBilling */

$this->title = Yii::t('outbound/titles', 'Order №').$model->order_number;
$this->params['breadcrumbs'][] = ['label' => Yii::t('outbound/titles', 'Reports: orders'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="report-order-view">

    <h1><?= Html::encode($this->title) ?></h1>

	<?= Html::a('Export to Excel Data Matrix',["/outbound/report/export-to-excel-data-matrix","id"=>$model->id],['class' => 'btn btn-success']) ?>
	<?= Html::a(
	Yii::t('outbound/forms', 'Edit destination'),
			["/outbound/report/update-to-point", "id" => $model->id],
			['class' => 'btn btn-primary']
	) ?>
	 <?= Html::a(
            Yii::t('outbound/forms', 'Equalize'),
            ["/outbound/report/equalize-expected-allocated", "id" => $model->id],
            ['class' => 'btn btn-warning']
        ) ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
           'parent_order_number',
           'order_number',
           'to_point_title',
            [
                'attribute' => 'status',
                'value' => $model->getStatusValue(),
            ],
            [
                'attribute' => 'cargo_status',
                'value' => $model->getCargoStatusValue(),
            ],
            'mc',
            'kg',
            'expected_qty',
            'accepted_qty',
            'allocated_qty',
            'expected_number_places_qty',
            'accepted_number_places_qty',
            'allocated_number_places_qty',
//            'expected_datetime:datetime',
//            'begin_datetime:datetime',
//            'end_datetime:datetime',
            [
                'attribute' => 'data_created_on_client',
                'value' => $model->data_created_on_client ? Yii::$app->formatter->asDatetime($model->data_created_on_client) : '-',
            ],
            [
                'attribute' => 'packing_date',
                'value' => $model->packing_date ? Yii::$app->formatter->asDatetime($model->packing_date) : '-',
            ],
            [
                'attribute' => 'date_left_warehouse',
                'value' => $model->date_left_warehouse ? Yii::$app->formatter->asDatetime($model->date_left_warehouse) : '-',
            ],
            [
                'attribute' => 'date_delivered',
                'value' => $model->date_delivered ? Yii::$app->formatter->asDatetime($model->date_delivered) : '-',
            ],
            'created_at:datetime',
            'updated_at:datetime',
//            [
//                'attribute' => 'created_at',
//                'value' => $model->updated_at ? Yii::$app->formatter->asDatetime($model->created_at) : '-',
//            ],
//            [
//                'attribute' => 'updated_at',
//                'value' => $model->updated_at ? Yii::$app->formatter->asDatetime($model->updated_at) : '-',
//            ],
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
        'product_sku',
        'product_model',
        'product_name',
        'expected_qty',
        'accepted_qty',
        'allocated_qty',

    ],
]);
?>
<?= html::a(
	yii::t('titles', 'Удалить накладную!!!'),
	["/outbound/report/delete-order", "id" => $model['id']],
	['class' => 'btn btn-danger  pull-right',
		'data' => [
			'confirm' => yii::t('titles', 'Вы действительно хотите УДАЛИТЬ накладную?'),
			'method' => 'post',
		],
	]
);
?>

<?= html::a(
	yii::t('titles', 'Сбросить накладную до статус "новый"!!!'),
	["/outbound/report/reset-order", "id" => $model['id']],
	['class' => 'btn btn-warning  pull-left',
		'data' => [
			'confirm' => yii::t('titles', 'Вы действительно хотите сбросить накладную?'),
			'method' => 'post',
		],
	]
);
?>