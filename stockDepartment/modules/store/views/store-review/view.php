<?php

use yii\helpers\Html;
use kartik\detail\DetailView;
use common\modules\transportLogistics\components\TLHelper;
use common\modules\audit\models\Audit;

/* @var $this yii\web\View */
/* @var  $model common\modules\store\models\StoreReviews */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('titles', 'Store Reviews'), 'url' => ['index']];
$this->params['breadcrumbs'][] = TLHelper::getProposalLabel($model->client_id, $model->tl_delivery_proposal_id);
?>
<div class="store-reviews-view">

    <h1><?= Html::encode(TLHelper::getProposalLabel($model->client_id, $model->tl_delivery_proposal_id)) ?></h1>

    <p>
<!--        --><?//= Html::a(Yii::t('forms', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
<!--        --><?//= Html::a(Yii::t('buttons', 'Delete'), ['delete', 'id' => $model->id], [
//            'class' => 'btn btn-danger',
//            'data' => [
//                'confirm' => Yii::t('forms', 'Are you sure you want to delete this item?'),
//                'method' => 'post',
//            ],
//        ]) ?>
        <?= Audit::haveAuditOrNot($model->id, 'StoreReviews') ? Html::a(Yii::t('titles', 'Show changelog'), ['/audit/default/index', 'parent_id' => $model->id, 'classname' => 'StoreReviews'], ['class' => 'btn btn-info']) : '' ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'enableEditMode'=>false,
        'attributes' => [
            'id',
            [
                'attribute'=>'client_id',
                'value' => (!empty($model->client) ? $model->client->username : ''),
//                'value' => $model->client->username,

            ],
//            [
//                'attribute'=>'store_id',
//                'value' => $model->store->name,
//            ],

            [
                'attribute' => 'tl_delivery_proposal_id',
                'value'=> TLHelper::getProposalLabel($model->client_id, $model->tl_delivery_proposal_id),

            ],
            'number_of_places',
            'delivery_datetime',
            'rate',
            'comment',
//            [
//                'attribute'=>'created_user_id',
//                'value' => $model::getUserName($model->created_user_id),
//            ],
//            [
//                'attribute'=>'updated_user_id',
//                'value' => $model::getUserName($model->updated_user_id),
//            ],
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

</div>
