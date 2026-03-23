<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel stockDepartment\modules\inbound\models\InboundOrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('return/titles', 'Report: return orders');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="return-order-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= Html::a(Yii::t('buttons', 'Clear search'), ['ttn-report'], ['class' => 'btn btn-primary']) ?>
    <?= Html::a("Отчет по коробам", ['search'], ['class' => 'btn btn-success']) ?>
    <?= Html::a("Размещенные: ".$countWithSecondaryAddress, ['ttn-report','ReturnTmpOrderTTNSearch[countWithSecondaryAddress]'=>1], ['class' => 'btn btn-warning pull-right','style'=>"margin:0 5px;"]) ?>
    <?= Html::a("Не размещенные: ".$countWithoutSecondaryAddress , ['ttn-report','ReturnTmpOrderTTNSearch[countWithoutSecondaryAddress]'=>1], ['class' => 'btn btn-danger pull-right','style'=>"margin:0 5px;"]) ?>
    <?= Html::a("Отправлены по API: ".$countSendByAPI, ['ttn-report','ReturnTmpOrderTTNSearch[countSendByAPI]'=>1], ['class' => 'btn btn-default pull-right','style'=>"margin:0 5px;"]) ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'rowOptions'=> function ($item, $key, $index, $grid) use ($deliveryProposals) {
            $qty = isset($deliveryProposals[$item->ttn]) ? $deliveryProposals[$item->ttn]['number_places'] : 0;
            $class = ($item->qty != $qty) ? 'color-add-route' : '';

            return ['class'=>$class];
        },
        'columns' => [
            [
                'label'=>"ТТН",
                'attribute'=>'ttn',
                'format'=>'raw',
                'value'=> function($item) use ($deliveryProposals,$storeArray) {

                    $strForShow  = $item->ttn;
                    $strForShow .= " / ";
                    $strForShow .= isset($storeArray[$deliveryProposals[$item->ttn]['route_from']]) ? $storeArray[$deliveryProposals[$item->ttn]['route_from']] : '-';

                    return \yii\bootstrap\Html::a($strForShow,['search','ReturnTmpOrderSearch[ttn]'=>$item->ttn]);
                }
            ],
            [
                'label'=>"кол-во отск-ое",
                'attribute'=>'qty'
            ],
            [
                'label'=>"кол-во по ТТН",
                'attribute'=>'qty',
                'value'=> function($item) use ($deliveryProposals) {
                    return isset($deliveryProposals[$item->ttn]) ? $deliveryProposals[$item->ttn]['number_places'] : 0;
                }
            ],


        ]
    ]); ?>
</div>
