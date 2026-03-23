<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 30.01.15
 * Time: 17:43
 */

use yii\helpers\Html;
use yii\helpers\Url;
use common\modules\store\models\Store;
use common\helpers\iHelper;

$this->title = Yii::t('outbound/titles', 'Report: outbound orders');
$this->params['breadcrumbs'][] = $this->title;
?>

    <h1><?= Html::encode($this->title) ?></h1>
<?= $this->render('_search', ['model' => $searchModel, 'clientsArray' => $clientsArray]); ?>

<?= \yii\grid\GridView::widget([
    'id' => 'grid-view-order-items',
    'dataProvider' => $activeDataProvider,
    'rowOptions' => function ($model, $key, $index, $grid) {
        $class = iHelper::getStockGridColor($model->status);
        return ['class' => $class];
    },
    'columns' => [
        [
            'attribute'=>'action',
            'format'=>'raw',
            'value'=>function($data) {
                if(in_array($data->status,[
                    \common\modules\stock\models\Stock::STATUS_OUTBOUND_NEW,
                    \common\modules\stock\models\Stock::STATUS_OUTBOUND_FULL_RESERVED,
                    \common\modules\stock\models\Stock::STATUS_OUTBOUND_PART_RESERVED,
                    \common\modules\stock\models\Stock::STATUS_OUTBOUND_PICKING,
                    \common\modules\stock\models\Stock::STATUS_OUTBOUND_PICKED,
                    \common\modules\stock\models\Stock::STATUS_OUTBOUND_PRINTED_PICKING_LIST,
                ])) {
                    return \yii\bootstrap\Html::a("Удалить",Url::toRoute(['delete-outbound','id'=>$data->id]),['class'=>'btn btn-danger delete-outbound']);
                }
                return \yii\bootstrap\Html::a("Удалить",Url::toRoute(['delete-outbound','id'=>$data->id]),['class'=>'btn btn-danger delete-outbound hidden']);
            },
        ],
        [
            'attribute' => 'order_number',
            'format' => 'html',
            'value' => function ($data) {
                return Html::tag('a', $data->order_number, ['href' => Url::to(['/outbound/report/view', 'id' => $data->id]), 'target' => '_blank']);
            },
        ],
        [
            'attribute' => 'to_point_id',
            'value' => function ($data) use ($storesArray) {
                return \common\overloads\ArrayHelper::getValue($storesArray, $data->to_point_id);
            },
        ],
        'expected_qty',
        [
            'attribute' => 'allocated_qty',
            'contentOptions' => function ($model, $key, $index, $column) {
                return ['id' => 'allocated-qty-cell-' . $model->id];
            }
        ],
        'accepted_qty',
        'packing_date:datetime',
        'date_left_warehouse:datetime',
        [
            'attribute' => 'status',
            'value' => function ($model) {
                return $model->getStatusValue();
            }
        ],
    ],
]); ?>

<script type="text/javascript">
    $(function(){
        $('.delete-outbound').on('click',function() {
           if(confirm("вы действидельно ходите удалить накладную")) {
               return true;
           }
           return false;
       })
    });
</script>
