<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use common\modules\store\models\Store;
use common\helpers\iHelper;

$this->title = Yii::t('outbound/titles', 'Грузим в машину ЗАКАЗЫ');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cross-dock-order-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= Html::a('Кросс-док ' . $dataProviderCrossDock->getTotalCount(), Url::to('/tms/box-out/cross-dock-list'), ['class' => 'btn btn-success']) ?>
    <?= Html::a('Заказы ' . $dataProviderOutbound->getTotalCount(), Url::to('/tms/box-out/outbound-list'), ['class' => 'btn btn-danger']) ?>
    <?= GridView::widget([
//    'id' => 'grid-view-order-items',
        'dataProvider' => $dataProviderOutbound,
        'rowOptions' => function ($model, $key, $index, $grid) {
            $class = iHelper::getStockGridColor($model->status);
            return ['class' => $class];
        },
        'columns' => [
            [
                'attribute' => 'id',
                'format' => 'html',
                'value' => function ($data) {
                    return Html::tag('a', $data->id, ['href' => Url::to(['view', 'id' => $data->id]), 'target' => '_blank']);
                },
            ],
            [
                'label' => 'Номер заказа',
                'attribute' => 'parent_order_number',
                'value' => function ($model) {
                    return $model->parent_order_number . ' / ' . $model->order_number;
                }
            ],
            [
                'attribute' => 'to_point_title',
                'value' => function ($model) use ($clientStoreArray) {
                    return \yii\helpers\ArrayHelper::getValue($clientStoreArray, $model->to_point_id);
                }
            ],
            [
                'label' => 'Кол-во мест',
                'attribute' => 'accepted_number_places_qty',
                'value' => function ($model) {
                    return $model->accepted_number_places_qty;
                }
            ],
            'packing_date:datetime',
            [
                'attribute' => 'actions',
                'label' => "Отгрузили",
                'format' => 'raw',
                'value' => function ($model) {
                    return \yii\helpers\Html::a(
                        'Отгрузили',
                        Url::to(['/tms/box-out/outbound-send-by-api?id=' . $model->id]),
                        [
                            'class' => 'btn btn-primary',
                            'style' => ' margin-left:10px;',
                        ]);
                },
            ]
        ],
    ]); ?>

</div>