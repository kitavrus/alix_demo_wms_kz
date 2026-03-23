<?php

use yii\helpers\Html;
use kartik\grid\DataColumn;
use kartik\grid\GridView;
use common\modules\store\models\Store;
use app\modules\transportLogistics\transportLogistics;
use yii\helpers\ArrayHelper;
use clientDepartment\modules\transportLogistics\components\TLHelper;

/* @var $this yii\web\View */
/* @var $searchModel clientDepartment\modules\store\models\StoreReviewSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('titles', 'Store Reviews');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="store-reviews-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

<!--    <p>-->
<!--        --><?//= Html::a(Yii::t('forms', 'Create {modelClass}', [
//    'modelClass' => 'Store Reviews',
//]), ['create'], ['class' => 'btn btn-success']) ?>
<!--    </p>-->

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

//            'id',
//            [
//                'class' => DataColumn::className(),
//                'attribute' => 'client_id',
//                'value' => 'client.username',
//                'filterType' => GridView::FILTER_SELECT2,
//                'filterWidgetOptions' => [
//                    'data' => $searchModel->getClientArray(),
//                    'options' => [
//                        'placeholder' => Yii::t('titles', 'Select client')
//                    ],
//                ],
//            ],
//            [
//                'class' => DataColumn::className(),
//                'attribute' => 'store_id',
//                'value' => 'store.name',
//                'filterType' => GridView::FILTER_SELECT2,
//                'filterWidgetOptions' => [
//                    'data' => ArrayHelper::map(Store::find()->all(),'id','name'),
//                    'options' => [
//                        'placeholder' => Yii::t('titles', 'Select store')
//                    ],
//                ],
//            ],
            [
                'class' => DataColumn::className(),
                'attribute' => 'tl_delivery_proposal_id',
                'value' => function ($model) {
                     return TLHelper::getProposalLabel($model->client_id, $model->tl_delivery_proposal_id);
                    },
            ],
            'delivery_datetime:datetime',
            'number_of_places',
            'rate',
            'comment',
            // 'created_user_id',
            // 'updated_user_id',
            // 'created_at',
            // 'updated_at',

            ['class' => '\kartik\grid\ActionColumn', 'updateOptions' => ['label'=> '<i class="hidden"></i>'],'deleteOptions' => ['label'=> '<i class="hidden"></i>']],
        ],
    ]); ?>

</div>
