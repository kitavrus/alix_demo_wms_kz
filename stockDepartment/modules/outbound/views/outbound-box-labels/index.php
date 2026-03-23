<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;
use kartik\grid\DataColumn;
use common\modules\store\models\Store;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('outbound/titles', 'Outbound Box Labels');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="outbound-box-labels-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

<!--    <p>-->
<!--        --><?//= Html::a(Yii::t('outbound/titles', 'Create Outbound Box Labels'), ['create'], ['class' => 'btn btn-success']) ?>
<!--    </p>-->

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//            'id',
            //'outbound_order_number',
            [
                'attribute'=>  'outbound_order_number',
                'value'=>function ($model) {
                    if($order = \common\modules\crossDock\models\CrossDock::findOne(['order_number'=>$model->outbound_order_number])) {
                        return  $model->outbound_order_number. " : Cross-Dock";
                    }

                    if($order = \common\modules\outbound\models\OutboundOrder::findOne(['id'=>$model->outbound_order_id])) {
                        return  $order->parent_order_number.' '.$model->outbound_order_number;
                    }
                    return $model->outbound_order_number;
                }
            ],
            [
                'attribute'=>  'to_point_title',
                'value'=>function ($model) {

                    if($order = \common\modules\crossDock\models\CrossDock::findOne(['order_number'=>$model->outbound_order_number])) {
                        if($store = Store::findOne(['id'=>$order->to_point_id])) {
                            return Store::getPointTitle($store->id);
                        }
                    }

                    if($order = \common\modules\outbound\models\OutboundOrder::findOne(['id'=>$model->outbound_order_id])) {
                        if($store = Store::findOne(['id'=>$order->to_point_id])) {
                            return Store::getPointTitle($store->id);
                        }
                    }
                    return  '-МАГАЗИН НЕ НАЙДЕН-';
                }
            ],
            [
                'class' => DataColumn::className(),
                'attribute' => 'client_id',
                'format'=> 'html',
                'value' => function($data) use ($clientArray){
                    if(isset($clientArray[$data->client_id])){
                        return Html::tag('a', $clientArray[$data->client_id], ['href'=>Url::to(['/client/default/view', 'id' => $data->client_id]), 'target'=>'_blank']);
                    }
                    return Yii::t('titles', 'Not set');
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => $clientArray,
                    'options' => [
                        'placeholder' => Yii::t('transportLogistics/forms', 'Select client')
                    ],
                ],
            ],
//            'outbound_order_id',
//            'outbound_order_number',
//            'box_label_url:url',
            // 'created_user_id',
            // 'updated_user_id',
            // 'created_at',
            // 'updated_at',
            // 'deleted',

            [
                'attribute'=>'actions',
                'label' => Yii::t('outbound/forms','Actions'),
                'format' => 'raw',
                'value' => function($model) {
                    $bt = \yii\helpers\Html::tag('span', Yii::t('outbound/buttons', 'Download PDF'),
                        [
                            'class' => 'btn btn-primary btn-href',
                            'style' => ' margin-left:10px;',
                            'data-url'=>Url::to(['download-label-pdf?id='.$model->id])
                        ]);

                    return $bt;
                },
            ]
        ],
    ]); ?>

</div>
