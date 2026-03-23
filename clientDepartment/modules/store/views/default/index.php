<?php

use common\modules\store\models\Store;
use common\modules\client\models\ClientEmployees;
use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\grid\EditableColumn;
use kartik\grid\DataColumn;

/* @var $this yii\web\View */
/* @var $searchModel clientDepartment\modules\store\models\StoreSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('titles', 'Stores');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="store-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('buttons', 'Create Store'), ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a(Yii::t('titles', 'Clear search'), ['index'], ['class' => 'btn btn-primary', 'style' => 'float:right; margin-left:10px;margin-top: -50px;']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'floatHeader' => true,
        'columns' => [
//            [
//
//                'attribute' => 'type_use',
//                'value' => function ($model) {
//                    return $model::getTypeUseArray($model->type_use);
//                },
//                'filter' => $searchModel::getTypeUseArray(),
//            ],


//            [
//                'class' => DataColumn::className(),
//                'name' => 'type_use',
//                'attribute' => 'type_use',
//                'value' => 'client.username',
//                'filterType' => GridView::FILTER_SELECT2,
//                'filterWidgetOptions' => [
//                    'data' => $searchModel->getTypeUseArray(),
//                    'options' => [
//                        'placeholder' => Yii::t('forms', 'Select use type'),
//                    ],

//                ],
//                'value' => function ($model) {
//                    return $model::getTypeUseArray($model->type_use);
//                },
//                'filter' => $searchModel::getTypeUseArray(),
//
//            ],
//            [
//                'class' => DataColumn::className(),
//                'attribute' => 'client_id',
//                'value' => 'client.username',
//                'filterType' => GridView::FILTER_SELECT2,
//                'filterWidgetOptions' => [
//                    'data' => $searchModel->getClientArray(),
//                    'options' => [
//                        'placeholder' => Yii::t('forms', 'Select client'),
//                    ],
//
//                ],
//
//            ],
            'name',
            'shopping_center_name',
            [
                'class' => DataColumn::className(),
                'attribute' => 'city_id',
                'value' => 'city.name',
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => $searchModel->getCityArray(),
                    'options' => [
                        'placeholder' => Yii::t('forms', 'Select city'),
                    ],
                ],

            ],
            'street',
            'house',
            [
                'format'=>'html',
                'attribute' => 'contact_first_name',
                'value' => function ($model) {

                    $clientEmployees  = ClientEmployees::find()
                                        ->where([
                                            'deleted'=>0,
                                            'client_id'=>$model->client_id,
                                            'store_id'=>$model->id,
                                            'manager_type'=>[
                                                ClientEmployees::TYPE_DIRECTOR,
                                                ClientEmployees::TYPE_DIRECTOR_INTERN,
                                                ClientEmployees::TYPE_MANAGER,
                                                ClientEmployees::TYPE_MANAGER_INTERN,
                                            ]
                                        ])
                                        ->all();
                    $html = '';
                    foreach($clientEmployees as $item) {
                        $html .= $item->username.' : '.$item->getType()."<br />";
                    }


                    return $html;
                },
            ],
//            'contact_first_name',
//            'contact_last_name',
            [
                'class' => DataColumn::className(),

                'attribute' => 'status',
                'value' => function ($model) {
                    return $model::getStatusArray($model->status);
                },
                'filter' => $searchModel::getStatusArray(),
            ],
            ['class' => 'yii\grid\ActionColumn',
                'template'=>'{view} {update} {delete}',
                'buttons'=>[
                    'delete'=> function ($url, $model, $key) {
                        $a =  Html::a(Yii::t('buttons', 'Delete'), ['delete', 'id' => $model->id], [
                            'class' => 'btn btn-danger btn-grid-action-column',
                            'data' => [
                                'confirm' => Yii::t('titles', 'Are you sure you want to delete this item?'),
                                'method' => 'post',
                            ],
                        ]);
                        return  $a.'<br />';
                    },
                    'update'=> function ($url, $model, $key) {
                        return  Html::a(Yii::t('buttons','Edit'), $url,['class'=>'btn btn-warning btn-grid-action-column']).'<br />';
                    },
                    'view'=> function ($url, $model, $key) {
                        return Html::a(Yii::t('buttons','View'), $url,['class'=>'btn btn-primary btn-grid-action-column']).'<br />';
                    },
                ]
            ],
        ],
    ]); ?>

</div>
