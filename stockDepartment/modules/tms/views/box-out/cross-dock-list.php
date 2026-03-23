<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use common\modules\store\models\Store;

/* @var $this yii\web\View */
/* @var $searchModel stockDepartment\modules\inbound\models\InboundOrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('outbound/titles', 'Грузим в машину КРОСС-ДОК');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cross-dock-order-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= Html::a('Кросс-док ' . $dataProviderCrossDock->getTotalCount(), Url::to('/tms/box-out/cross-dock-list'), ['class' => 'btn btn-success']) ?>
    <?= Html::a('Заказы ' . $dataProviderOutbound->getTotalCount(), Url::to('/tms/box-out/outbound-list'), ['class' => 'btn btn-danger']) ?>
    <?= GridView::widget([
        'dataProvider' => $dataProviderCrossDock,
//        'id' => 'cross-dock-order-report',
        'columns' => [
            [
                'attribute' => 'id',
                'format' => 'html',
                'value' => function ($data) {
                    return Html::tag('a', $data->id, ['href' => Url::to(['view', 'id' => $data->id]), 'target' => '_blank']);
                },
            ],
            [
                'attribute' => 'internal_barcode',
                'label' => 'Номер заказа',
                'value' => function ($model) {
                    return $model->party_number . ' / ' . ltrim($model->internal_barcode, '2-');
                }
            ],
            [
                'attribute' => 'to_point_id',
                'label' => 'Код магазина',
                'value' => function ($model) {
                    $storeTitle = '-МАГАЗИН НЕ НАЙДЕН-';
                    if ($store = Store::findOne($model->to_point_id)) {
                        $storeTitle = Store::getPointTitle($store->id);
                    }
                    return $storeTitle;
                }
            ],
            [
                'attribute' => 'accepted_number_places_qty',
                'label' => 'Кол-во мест',
                'value' => function ($model) {
                    return $model->accepted_number_places_qty;
                }
            ],
            [
                'attribute' => 'accepted_datetime',
                'label' => 'Дата упаковки',
                'value' => function ($model) {
                    return Yii::$app->formatter->asDatetime($model->accepted_datetime);
                }
            ],
            [
                'attribute' => 'actions',
                'label' => "Отгрузили",
                'format' => 'raw',
                'value' => function ($model) {
                    return \yii\helpers\Html::a(
                        'Отгрузили',
                        Url::to(['/tms/box-out/cross-dock-send-by-api?id=' . $model->id]),
                        [
                            'class' => 'btn btn-primary',
                            'style' => ' margin-left:10px;',
                        ]);
                },
            ]
        ],
    ]); ?>
</div>