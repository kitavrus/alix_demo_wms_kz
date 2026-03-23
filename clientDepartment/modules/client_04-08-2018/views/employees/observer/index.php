<?php

use yii\helpers\Html;
//use yii\grid\GridView;
use kartik\grid\GridView;
use kartik\grid\EditableColumn;
use kartik\grid\DataColumn;
use common\modules\transportLogistics\components\TLHelper;

/* @var $this yii\web\View */
/* @var $searchModel clientDepartment\modules\client\models\ClientEmployeesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('titles', 'Employees');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-managers-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
<!--        --><?//= Html::a(Yii::t('titles', 'Добавить сотрудника', [
//        ]), ['create'], ['class' => 'btn btn-success']) ?>

        <?= Html::a(Yii::t('titles', 'Clear search'), ['index'], ['class' => 'btn btn-primary', 'style' => 'float:right; margin:10px;margin-top: -50px;']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],
            [
                'class' => DataColumn::className(),
                'attribute' => 'store_id',
                'value' => function ($model) {
                    $v = '-';
                    if($store = $model->store) {
                        $v = $store->getDisplayFullTitle();
                    }

                    return $v;
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => TLHelper::getStoreArrayByClientID(),
                    'options' => [
                        'placeholder' => Yii::t('forms', '-'),
                    ],
                ],
            ],
            'username',
            'first_name',
            'middle_name',
            'last_name',
            [
                'attribute' => 'phone',
                'value' => function ($model) {
                    return $model->phone . ' / ' . $model->phone_mobile;
                },
            ],
            'email:email',
            [
                'class' => DataColumn::className(),
                'attribute' => 'manager_type',
                'value' => function ($model) {
                    return $model->getType();
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => $searchModel->getTypeArray(),
                    'options' => [
                        'placeholder' => Yii::t('titles', '-')
                    ],
                ],
            ],
            [
                'class' => DataColumn::className(),
                'attribute' => 'status',
                'value' => function ($model) {
                    return $model->getStatus();
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => $searchModel->getStatusArray(),
                    'options' => [
                        'placeholder' => Yii::t('titles', '-')
                    ],
                ],
            ],
            ['class' => 'yii\grid\ActionColumn',
                'template'=>'{view}',
                'buttons'=>[
//                    'delete'=> function ($url, $model, $key) {
//
//                        $a = '';
//
//                        if($model->user_id != Yii::$app->user->id) {
//                            $a =  Html::a(Yii::t('buttons', 'Delete'), ['delete', 'id' => $model->id], [
//                                'class' => 'btn btn-danger btn-grid-action-column',
//                                'data' => [
//                                    'confirm' => Yii::t('titles', 'Are you sure you want to delete this item?'),
//                                    'method' => 'post',
//                                ],
//                            ]);
//                        }
//
//                        return  $a.'<br />';
//                    },
//                    'update'=> function ($url, $model, $key) {
//                        return  Html::a(Yii::t('buttons','Edit'), $url,['class'=>'btn btn-warning btn-grid-action-column']).'<br />';
//                    },
                    'view'=> function ($url, $model, $key) {
                        return Html::a(Yii::t('buttons','View'), $url,['class'=>'btn btn-primary btn-grid-action-column']).'<br />';
                    },
                ]
            ],

        ],
    ]); ?>
</div>
