<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\VarDumper;
use yii\widgets\DetailView;
use yii\grid\GridView;
use common\modules\transportLogistics\models\TlDeliveryProposalOrders;
use app\modules\transportLogistics\transportLogistics;
use common\modules\store\models\StoreReviews;
use clientDepartment\modules\client\components\ClientManager;

/* @var $this yii\web\View */
/* @var $model common\modules\transportLogistics\models\TlDeliveryProposal */
/* @var $dataProviderProposalRoutes common\modules\transportLogistics\models\TlDeliveryRoutes */
/* @var $dataProviderProposalOrders yii\data\ActiveDataProvider */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('transportLogistics/titles', 'Tl Delivery Proposals'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
    <div class="tl-delivery-proposal-view">

        <h1><?= Html::encode(Yii::t('transportLogistics/titles','Request for delivery')).'   № '.$model->id ?></h1>

        <p>
            <?php if(ClientManager::canUpdateDeliveryProposal($model) ) { ?>
                <?= Html::a(Yii::t('transportLogistics/buttons', 'Edit'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?php } ?>

            <?php if(ClientManager::canDeleteDeliveryProposal($model) ) { ?>
            <?= Html::a(Yii::t('transportLogistics/buttons', 'Delete'), ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('transportLogistics/forms', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]) ?>
            <?php } ?>
			<?= Html::a(Yii::t('transportLogistics/buttons', 'Добавить короба'), ['/transportLogistics/order-boxes/scanning', 'delivery_proposal_id' => $model->id], ['class' => 'btn btn-warning']) ?>
        </p>

        <?=
        DetailView::widget([
            'model' => $model,
            'attributes' => [
                'client_ttn',
                [
                    'attribute'=> 'route_from',
                    'value' => $model::getRouteFromTo($model->route_from),

                ],
                [
                    'attribute'=> 'route_to',
                    'value' => $model::getRouteFromTo($model->route_to),

                ],
                'shipped_datetime:datetime',
                'expected_delivery_date:datetime',
                'delivery_date:datetime',
                'mc',
                'mc_actual',
                [
                    'attribute'=> 'kg',
                    'visible' => ClientManager::canViewAttribute($model),

                ],
                [
                    'attribute'=> 'kg_actual',
                    'visible' => ClientManager::canViewAttribute($model),

                ],
                'number_places',
                'number_places_actual',
                [
                    'attribute'=> 'cash_no',
                    'value' => $model::getPaymentMethodArray($model->cash_no),

                ],
                'price_invoice',
                'price_invoice_with_vat',
                [
                    'attribute'=> 'status',
                    'value' => $model->getStatusForClient(),
//                    'value' => $model::getStatusArray($model->status),

                ],
                [
                    'attribute'=> 'status_invoice',
                    'value' => $model::getInvoiceStatusArray($model->status_invoice),

                ],
                'comment:ntext',
                [
                    'attribute'=> 'created_user_id',
                    'value' => $model::getUserName($model->created_user_id),

                ],
                [
                    'attribute'=> 'updated_user_id',
                    'value' => $model::getUserName($model->updated_user_id),

                ],
                'created_at:datetime',
                'updated_at:datetime',
            ],
        ]) ?>
    </div>

<?php //if($model->is_client_confirmed == $model::IS_CLIENT_CONFIRMED_WAITING) {  ?>
<!--    <h3 id="title-is-client-confirmed">-->
<!--        --><?//= Html::encode(Yii::t('transportLogistics/custom','Данные в заявке указаны')) . ' :  '
//            . Html::a(
//            Yii::t('transportLogistics/custom', 'Верно'),
//            ['is-client-confirm','id'=>$model->id],
//            ['class' => 'btn btn-danger','data' => [
//            'confirm' => Yii::t('transportLogistics/custom', 'Вы точно хотите подтвердить выбор??'),
//            'method' => 'post',
//            ]]) ?>
<!--    </h3>-->
<?php //} ?>


<?php if($dataProviderProposalOrders->getTotalCount()) { ?>

<h1 id="title-order">
    <?= Html::encode(Yii::t('transportLogistics/titles','Orders')) ?>
</h1>

<?=
GridView::widget([
    'dataProvider' => $dataProviderProposalOrders,
    'columns' => [
//        'order_id',
        'order_number',
        'number_places',
        'mc_actual',
        [
            'attribute'=> 'order_type',
            'value' => function ($model) {
                return TlDeliveryProposalOrders::getOrderTypeValue($model->order_type);},
        ],
        [
            'attribute'=> 'delivery_type',
            'value' => function ($model) {return TlDeliveryProposalOrders::getDeliveryTypeValue($model->delivery_type);},
        ],
    ],
]); ?>

<?php } ?>
<?php if($soreReview =  StoreReviews::findOne(['tl_delivery_proposal_id'=>$model->id])) { ?>
    <?php $soreReview->scenario = 'update';?>
    <?= $this->render('_store-review-view',['model'=>$soreReview,'storeReviewButton1'=>$storeReviewButton1]) ?>
<?php } ?>
