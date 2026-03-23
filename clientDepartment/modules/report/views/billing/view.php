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

<!--    <p>-->
<!--        --><?//= Html::a(Yii::t('buttons', 'Edit'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
<!--        --><?//= Html::a(Yii::t('buttons', 'Delete'), ['delete', 'id' => $model->id], [
//            'class' => 'btn btn-danger',
//            'data' => [
//                'confirm' => Yii::t('forms', 'Are you sure you want to delete this item?'),
//                'method' => 'post',
//            ],
//        ]) ?>
<!--    </p>-->

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute' => 'rule_type',
                'value' => $model->getRuleType(),
            ],
            'client.title',
//            'country_id',
//            'region_id',
//            'city_id',
            [
                'attribute' => 'route_from',
//                'type' => DetailView::INPUT_SELECT2,
                'widgetOptions'=>[
                    'data'=>TLHelper::getStoreArrayByClientID(),
                ],
                'value' => $model::getRouteFromTo($model->route_from),
            ],
            [
//                'type' => DetailView::INPUT_SELECT2,
                'widgetOptions'=>[
                    'data'=>TLHelper::getStoreArrayByClientID(),
                ],
                'attribute' => 'route_to',
                'value' => $model::getRouteFromTo($model->route_to),
            ],
//            'mc',
//            'kg',
//            'number_places',
            'price_invoice:currency',
            'price_invoice_with_vat:currency',
//            'formula_tariff:ntext',
            'status',
            'comment:ntext',
            [
//                'displayOnly' => true,
                'attribute' => 'created_user_id',
                'value' => $model::getUserName($model->created_user_id),

            ],
            [
//                'displayOnly' => true,
                'attribute' => 'updated_user_id',
                'value' => $model::getUserName($model->updated_user_id),

            ],
//            'created_at:datetime',
//            'updated_at:datetime',
        ],
    ]) ?>

</div>

<h1 id="title-cars">
    <?= Html::encode(Yii::t('titles','Правила для тарифа')) ?>
<!--    --><?//= Html::a(Yii::t('titles', 'Добавить новое правило'), ['/billing/condition/create', 'rule_id' => $model->id], ['class' => 'btn btn-primary', 'style' => 'float:right; ',]) ?>
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
//        ['class' => 'yii\grid\ActionColumn',
//            'template'=>'{update} {delete}',
//            'urlCreator'=>function( $action, $model, $key, $index) {
//
//                $params = ['id'=>$model->id];
//                $params[0] = '/billing/condition/' . $action;
//                $url = Url::toRoute($params);
//                return $url;
//            },
//            'buttons'=>[
//                'delete'=> function ($url, $model, $key) {
//                    return   Html::a(Yii::t('buttons', 'Delete'), $url, [
//                        'class' => 'btn btn-danger',
//                        'data' => [
//                            'confirm' => Yii::t('titles', 'Are you sure you want to delete this item?'),
//                            'method' => 'post',
//                        ],
//                    ]);
//                },
//
//                'update'=> function ($url, $model, $key) {
//                    return  Html::a(Yii::t('buttons', 'Edit'), $url,['class'=>'btn btn-primary']);
//                },
//            ]
//        ],
    ],
]);
?>