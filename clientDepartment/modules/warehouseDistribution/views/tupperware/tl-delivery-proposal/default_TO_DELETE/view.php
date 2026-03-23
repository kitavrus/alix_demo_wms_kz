<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\grid\GridView;
use common\modules\transportLogistics\models\TlDeliveryProposalOrders;
use app\modules\transportLogistics\transportLogistics;
use common\modules\store\models\StoreReviews;


/* @var $this yii\web\View */
/* @var $model common\modules\transportLogistics\models\TlDeliveryProposal */
/* @var $dataProviderProposalRoutes common\modules\transportLogistics\models\TlDeliveryRoutes */
/* @var $dataProviderProposalOrders yii\data\ActiveDataProvider */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('transportLogistics/titles', 'Tl Delivery Proposals'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
    <div class="tl-delivery-proposal-view">

        <h1><?= Html::encode(Yii::t('transportLogistics/custom','Request for delivery')).'   № '.$model->id ?></h1>

<!--        <p>-->
<!--            --><?//= Html::a(Yii::t('transportLogistics/buttons', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
<!--            --><?//=
//            Html::a(Yii::t('transportLogistics/buttons', 'Delete'), ['delete', 'id' => $model->id], [
//                'class' => 'btn btn-danger',
//                'data' => [
//                    'confirm' => Yii::t('transportLogistics/forms', 'Are you sure you want to delete this item?'),
//                    'method' => 'post',
//                ],
//            ]) ?>
<!--        </p>-->

        <?=
        DetailView::widget([
            'model' => $model,
            'attributes' => [

                [
                    'attribute'=> 'route_from',
                    'value' => $model::getRouteFromTo($model->route_from),

                ],
                [
                    'attribute'=> 'route_to',
                    'value' => $model::getRouteFromTo($model->route_to),

                ],
                'shipped_datetime:datetime',
                'delivery_date:datetime',
                'mc',
                'mc_actual',
                'kg',
                'kg_actual',
                'number_places',
                'number_places_actual',

                'comment:ntext',
//                [
//                    'attribute'=> 'created_user_id',
//                    'value' => $model::getUserName($model->created_user_id),
//
//                ],
//                [
//                    'attribute'=> 'updated_user_id',
//                    'value' => $model::getUserName($model->updated_user_id),
//
//                ],
                'created_at:datetime',
                'updated_at:datetime',
            ],
        ]) ?>
    </div>

<?php //if($model->is_client_confirmed == $model::IS_CLIENT_CONFIRMED_WAITING) {  ?>
<!--    <h3 id="title-is-client-confirmed">-->
<!--        --><?//= Html::encode(Yii::t('transportLogistics/custom','Данные в заявке указаны')) . ' :  '
//            . Html::a(
//            Yii::t('transportLogistics/custom', 'верно'),
//            ['is-client-confirm','id'=>$model->id],
//            ['class' => 'btn btn-danger','data' => [
//            'confirm' => Yii::t('transportLogistics/custom', 'Are you sure you want to confirm this item?'),
//            'method' => 'post',
//            ]]) ?>
<!--    </h3>-->
<?php //} ?>


<?php if($dataProviderProposalOrders->getTotalCount()) { ?>

<h1 id="title-order">
    <?= Html::encode(Yii::t('transportLogistics/custom','Заказы которые нужно доставить в этой заявке')) ?>
</h1>

<?=
GridView::widget([
    'dataProvider' => $dataProviderProposalOrders,
    'columns' => [
        'order_id',
        [
            'attribute'=> 'order_type',
            'value' => function ($model) {return TlDeliveryProposalOrders::getOrderTypeArray($model->order_type);},

        ],
    ],
]); ?>

<?php } ?>

<?= $this->render('_store-review-view',['model'=>StoreReviews::findOne(['tl_delivery_proposal_id'=>$model->id])]) ?>