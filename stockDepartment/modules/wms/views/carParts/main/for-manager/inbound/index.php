<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel stockDepartment\modules\inbound\models\InboundOrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('inbound/titles', 'Report: inbound orders');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="inbound-order-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel, 'clientsArray' => $clientsArray]); ?>

    <?= GridView::widget([
        'dataProvider' => $activeDataProvider,
        'id' => 'inbound-order-report',
        'columns' => [
            [
                'attribute'=>'action',
                'format'=>'raw',
                'value'=>function($data) {
                    if(in_array($data->status,[
                        \common\modules\stock\models\Stock::STATUS_INBOUND_NEW,
                    ])) {
                        return \yii\bootstrap\Html::a("удалить",Url::toRoute(['delete-inbound','id'=>$data->id]),['class'=>'btn btn-danger delete-inbound']);
                    }
                    return \yii\bootstrap\Html::a("удалить",Url::toRoute(['delete-inbound','id'=>$data->id]),['class'=>'btn btn-danger delete-inbound hidden']);
                },
            ],
//            [
//                'attribute' => 'id',
//                'format' => 'html',
//                'value' => function ($data) {
//                    return Html::tag('a', $data->id, ['href' => Url::to(['/inbound/report/view', 'id' => $data->id]), 'target' => '_blank']);
//                },
//            ],
            [
                'attribute' => 'order_number',
                'format' => 'html',
                'value' => function ($data) {
                    return Html::tag('a', $data->order_number, ['href' => Url::to(['/inbound/report/view', 'id' => $data->id]), 'target' => '_blank']);
                },
            ],
            [
                'attribute' => 'client_id',
                'value' => function ($data) use ($clientsArray) {
                    if (isset($clientsArray[$data->client_id])) {
                        return $clientsArray[$data->client_id];
                    }
                    return '-';
                },
            ],
            'expected_qty',
            'accepted_qty',
            'created_at:datetime',
            'begin_datetime:datetime',
            'date_confirm:datetime',
            [
                'attribute' => 'status',
                'filter' => $searchModel->getStatusArray(),
                'value' => function ($model) {
                    return $model->getStatusValue();
                },
            ],
        ],
    ]); ?>
</div>

<script type="text/javascript">
    $(function(){
        $('.delete-inbound').on('click',function() {
            if(confirm("вы действидельно ходите удалить накладную")) {
                return true;
            }
            return false;
        })
    });
</script>