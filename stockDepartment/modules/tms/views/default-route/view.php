<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\modules\transportLogistics\models\TlDeliveryProposalDefaultRoute */

$this->title = Yii::t('transportLogistics/titles', 'Default Route № {id}', ['id' => $model->id]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('transportLogistics/titles', 'Tl Delivery Proposal Default Routes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tl-delivery-proposal-default-route-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('transportLogistics/buttons', 'Edit'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('transportLogistics/buttons', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('titles', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
//            [
//                'attribute' => 'client_id',
//                'value' => $model->client->username,
//            ],
            [
                'attribute' => 'from_point_id',
                'value' => $model->fromPoint->getPointTitleByPattern('stock'),
            ],
            [
                'attribute' => 'to_point_id',
                'value' => $model->toPoint->getPointTitleByPattern('stock'),
            ],
            [
                'attribute' => 'created_user_id',
                'value' => $model::getUserName($model->created_user_id),

            ],
            [
                'attribute' => 'updated_user_id',
                'value' => $model::getUserName($model->updated_user_id),

            ],
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

</div>

<h1>
    <?= Html::encode(Yii::t('transportLogistics/titles','Sub Routes')) ?>
    <?= Html::a(Yii::t('transportLogistics/titles', 'Add new Sub Routes'), ['/tms/default-sub-route/create', 'default_route_id' => $model->id], ['class' => 'btn btn-primary', 'style' => 'float:right; ',]) ?>
</h1>


<?=
GridView::widget([
    'dataProvider' => new ActiveDataProvider([
        'query' => $model->getSubRoutes(),
        'sort' => false,
    ]),
    'afterRow' => function ($model, $key, $index, $grid) {
        return $this->render('_route_expenses-grid-view-after-row',['model'=>$model,'key'=>$key, 'index'=>$index, 'grid'=>$grid]);
    },
    'columns' => [

        [

            'attribute' => 'from_point_id',
            'value' => function ($data) use ($storeArray) {return isset($storeArray[$data->from_point_id]) ? $storeArray[$data->from_point_id] : '-';},
        ],
        [
            'attribute' => 'to_point_id',
            'value' => function ($data) use ($storeArray) {return isset($storeArray[$data->to_point_id]) ? $storeArray[$data->to_point_id] : '-';},
        ],
        [
            'label' => 'Субподрядчик и транспорт',
            'value' => function ($data)  {
                return $car = $data->car ? $data->car->getDisplayTitle() : '-';
            },
        ],
        [
            'attribute' => 'transport_type',
            'value' => function ($data) {return $data->getTransportTypeValue();},
//            'value' => function ($data) {$tt = $data->getTransportType();return $tt;},
        ],

        ['class' => 'yii\grid\ActionColumn',
            //'template'=>'{update} {delete} {changelog}',
            'template'=>'{update} {delete}',
            'urlCreator'=>function( $action, $model, $key, $index) {

                $params = ['id'=>$model->id];
                $params[0] = '/tms/default-sub-route/' . $action;
                $url = Url::toRoute($params);
//                }

                return $url;
            },
            'buttons'=>[
                'delete'=> function ($url, $model, $key) {
                    return   Html::a(Yii::t('buttons', 'Delete'), $url, [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => Yii::t('titles', 'Are you sure you want to delete this item?'),
                            'method' => 'post',
                        ],
                    ]);
                },

                'update'=> function ($url, $model, $key) {
                    return  Html::a(Yii::t('buttons', 'Edit'), $url,['class'=>'btn btn-primary']);
                },

//                'changelog'=> function ($url, $model, $key) {
//                    return  Audit::haveAuditOrNot($model->id, 'TlAgentBillingConditions') ? Html::a(Yii::t('titles', 'Show changelog'), ['/audit/default/index', 'parent_id' => $model->id, 'classname' => 'TlAgentBillingConditions'], ['class' => 'btn btn-info']) : '';
//                },
            ]
        ],
    ],
]);
?>
