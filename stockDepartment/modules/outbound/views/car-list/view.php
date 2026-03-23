<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 04.08.2015
 * Time: 15:27
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\grid\GridView;
use common\modules\transportLogistics\components\TLHelper;
use common\helpers\iHelper;
use common\modules\stock\models\Stock;

/* @var $this yii\web\View */
/* @var $model common\modules\billing\models\TlDeliveryProposalBilling */

$this->title = Yii::t('outbound/titles', 'Order №') . $model->order_number;
$this->params['breadcrumbs'][] = ['label' => Yii::t('outbound/titles', 'Reports: orders'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
    <div class="car-list-view">

        <h1><?= Html::encode($this->title) ?></h1>

        <p>

            <?php if(!Stock::find()->andWhere(['client_id' => $model->client_id, 'outbound_order_id' => $model->id])->count()){
                echo Html::a(
                            Yii::t('outbound/buttons', 'Print box label'),
                            Url::toRoute(['print-label-barcode', 'id' => $model->id, 'order_type' => \common\modules\transportLogistics\models\TlDeliveryProposalOrders::ORDER_TYPE_RPT]),
                            [
                                'class' => 'btn btn-primary',
                                'style' => ' margin-left:10px;',
                                'id' => 'outbound-order-print-barcode-bt',
                            ]
                        );

                        echo Html::a(Yii::t('buttons', 'Delete'), ['delete', 'id' => $model->id], [
                            'class' => 'btn btn-danger pull-right',
                            'data' => [
                                'confirm' => Yii::t('titles', 'Are you sure you want to delete this item?'),
                                'method' => 'post',
                            ]

                        ]);
                echo Html::a(Yii::t('buttons', 'Edit'), ['update', 'id' => $model->id], ['class' => 'btn btn-warning']);
            }?>


        </p>

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
                'title',
                'description',
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
        <?= Html::encode(Yii::t('outbound/titles', 'Order items')) ?>
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