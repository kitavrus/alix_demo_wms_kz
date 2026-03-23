<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\grid\GridView;
use common\modules\transportLogistics\components\TLHelper;
use common\helpers\iHelper;
use common\modules\stock\models\Stock;

/* @var $this yii\web\View */
/* @var $model common\modules\billing\models\TlDeliveryProposalBilling */

$this->title = Yii::t('inbound/titles', 'Cross-dock order №').$model->order_number;
$this->params['breadcrumbs'][] = ['label' => Yii::t('titles', 'Report: cross-dock orders'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="report-order-view">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
           'id',
           [
               'attribute'=>'client_id',
               'value'=>\yii\helpers\ArrayHelper::getValue($clientsArray,$model->client_id),
           ],
           'order_number',
            [
                'attribute'=>'from_point_id',
                'value'=> isset($clientStoreArray[$model->from_point_id]) ? $clientStoreArray[$model->from_point_id] : '-' ,
            ],
            [
                'attribute'=>'to_point_id',
                'value'=> isset($clientStoreArray[$model->to_point_id]) ? $clientStoreArray[$model->to_point_id] : '-' ,
            ],
//           'to_point_title',
//           'from_point_title',
           'internal_barcode',
           'party_number',
            [
                'attribute'=>'status',
                'value'=> isset($statusArray[$model->status]) ? $statusArray[$model->status] : '-' ,
            ],
           'accepted_number_places_qty',
           'expected_number_places_qty',
           'box_m3',
           'weight_net',
           'weight_brut',
        ],
    ]) ?>

</div>

<h1 id="title-cars">
    <?= Html::encode(Yii::t('titles','Order contains')) ?>
</h1>


<?=
GridView::widget([
    'dataProvider' => $ItemsProvider,
//    'filterModel' => $searchModel,
    'columns' => [
        'box_barcode',
        'box_m3',
        'weight_net',
        'weight_brut',
        'expected_number_places_qty',
        'accepted_number_places_qty',
    ],
]);
?>