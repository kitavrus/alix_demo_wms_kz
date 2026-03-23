<?php

use yii\helpers\Html;
use kartik\detail\DetailView;
use common\modules\transportLogistics\components\TLHelper;

/* @var $this yii\web\View */
/* @var $model common\modules\store\models\StoreReviews */

//$this->title = $model->id;
//$this->params['breadcrumbs'][] = ['label' => Yii::t('transportLogistics/titles', 'Store Reviews'), 'url' => ['index']];
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="store-reviews-view">

    <h1><?= Html::encode(Yii::t('transportLogistics/titles', 'Store Review')) ?></h1>

    <p>
        <!--        --><?//= Html::a(Yii::t('forms', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <!--        --><?//= Html::a(Yii::t('buttons', 'Delete'), ['delete', 'id' => $model->id], [
        //            'class' => 'btn btn-danger',
        //            'data' => [
        //                'confirm' => Yii::t('forms', 'Are you sure you want to delete this item?'),
        //                'method' => 'post',
        //            ],
        //        ]) ?>
    </p>

    <?= DetailView::widget([
//        'formOptions' => ['action'=>['/store/store-review/view','id'=>$model->id]],
//        'buttons1'=>$storeReviewButton1,
//        'buttons2'=>'',
//        'saveOptions'=>['label'=>Yii::t('titles','Сохранить')],
//        'viewOptions'=>['label'=>Yii::t('titles','Просмотр')],
//        'viewOptions'=>[''],
//        'updateOptions'=>['label'=>Yii::t('titles','Редактировать')],
        'model' => $model,
//        'panel' => [
//
//            'heading' => Html::encode(Yii::t('transportLogistics/titles', 'Store Review')) . '   № ' . $model->id,
//            'type' => DetailView::TYPE_SUCCESS,
//        ],
        'attributes' => [
            //'id',
//            [
//                'attribute'=>'client_id',
//                'value' => $model->client->username,
//                'displayOnly'=>true,
//
//            ],
//            [
//                'attribute'=>'store_id',
//                'value' => $model->store->name,
//                'displayOnly'=>true,
//            ],
            [
                'attribute'=>'delivery_datetime',
                'type' => DetailView::INPUT_DATETIME,

            ],
//            [
//
//
//                'attribute'=>'delivery_datetime',
//                'format'=>'datetime',
//                'type'=>DetailView::INPUT_DATETIME,
//                'widgetOptions'=>[
//                    'pluginOptions'=>['format'=>'yyyy-mm-dd hh:mm']
//                ],
//                'inputWidth'=>'40%'
//            ],
            [
                'attribute' => 'tl_delivery_proposal_id',
                'value'=>   TLHelper::getProposalLabel($model->client_id, $model->tl_delivery_proposal_id),
                'displayOnly'=>true,

            ],
            'number_of_places',
            [
                'attribute' => 'rate',
//                'type'=>DetailView::INPUT_STAR,
                'type'=>DetailView::INPUT_RATING,
                'widgetOptions'=>[
                    'pluginOptions'=>['step' => 1],
                ],

            ],
            [
                'attribute' => 'comment',
                'type'=>DetailView::INPUT_TEXTAREA,
                'row' =>6,

            ],
//            [
//                'attribute'=>'created_user_id',
//                'value' => $model::getUserName($model->created_user_id),
//            ],
//            [
//                'attribute'=>'updated_user_id',
//                'value' => $model::getUserName($model->updated_user_id),
//            ],
//            'created_at:datetime',
//            'updated_at:datetime',
        ],
    ]) ?>

</div>
