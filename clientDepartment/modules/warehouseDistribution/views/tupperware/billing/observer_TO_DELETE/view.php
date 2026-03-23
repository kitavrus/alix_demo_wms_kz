<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use common\modules\transportLogistics\components\TLHelper;

/* @var $this yii\web\View */
/* @var $model common\modules\billing\models\TlDeliveryProposalBilling */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('titles', 'Billings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tl-delivery-proposal-billing-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute' => 'rule_type',
                'value' => $model->getRuleType(),
            ],
            'client.title',
            [
                'attribute' => 'route_from',
                'widgetOptions'=>[
                    'data'=>TLHelper::getStoreArrayByClientID(),
                ],
                'value' => $model::getRouteFromTo($model->route_from),
            ],
            [
                'widgetOptions'=>[
                    'data'=>TLHelper::getStoreArrayByClientID(),
                ],
                'attribute' => 'route_to',
                'value' => $model::getRouteFromTo($model->route_to),
            ],
            'price_invoice:currency',
            'price_invoice_with_vat:currency',
            'status',
            'comment:ntext',

        ],
    ]) ?>

</div>

<h1 id="title-cars">
    <?= Html::encode(Yii::t('titles','Rules for tariff')) ?>
</h1>


<?=
GridView::widget([
    'dataProvider' => new ActiveDataProvider([
        'query' => $model->getConditions(),
    ]),
    'columns' => [
        'formula_tariff',
        'price_invoice:currency',
        'price_invoice_with_vat:currency',
        [
            'attribute' => 'status',
            'value' => function ($model) {
                return $model->getStatus();
            },
        ],
    ],
]);
?>