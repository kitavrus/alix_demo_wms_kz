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
	<?= Html::a('Загрузить и обновить Дата-Матрикс',["/intermode/outbound/dm/make/form","outbound_id"=>$model->id],['class' => 'btn btn-warning']) ?>

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
            'api_complete_status',
            'mc',
            // 'kg',
			[
				'attribute' => 'kg',
				'value' => $kg,
			],
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

<?= Html::a('Отправить данные по API',["/intermode/outbound/scanning/complete","id"=>$model->id],['class' => 'btn btn-danger']) ?>
