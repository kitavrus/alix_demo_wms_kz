<?php

use yii\helpers\Html;
use kartik\detail\DetailView;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use common\modules\client\models\Client;

/* @var $this yii\web\View */
/* @var $model common\modules\client\models\Client */

$this->title = Yii::t('titles', 'Profile view: ') . $model->username;

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-view">

    <h1><?= Yii::t('titles', 'Profile view: ') . $model->username ?></h1>

    <p>
        <?= Html::a(Yii::t('buttons', 'Edit'), ['update'], ['class' => 'btn btn-primary']) ?>
<!--        --><?//= Html::a(Yii::t('buttons', 'Delete'), ['delete'], [
//            'class' => 'btn btn-danger',
//            'data' => [
//                'confirm' => Yii::t('forms', 'Are you sure you want to delete this item?'),
//                'method' => 'post',
//            ],
//        ]) ?>
    </p>

    <?= DetailView::widget([
         'model' => $model,
         'attributes' => [
            'legal_company_name',
            'username',
            'first_name',
            'last_name',
            'middle_name',
            'phone',
            'phone_mobile',
            'email:email',
             [
                 'attribute'=>'status',
                 'value' => $model->getStatus(),
             ],
        ],
    ]) ?>

</div>

<h1 id="title-cars">
    <?= Html::encode(Yii::t('titles','Client manager')) ?>
<!--    --><?//= Html::a(Yii::t('titles', 'Create Client Manager'), ['/client/default/create-employee', 'client_id' => $model->id], ['class' => 'btn btn-primary', 'style' => 'float:right; ',]) ?>
    <?= Html::a(Yii::t('titles', 'Create Client Manager'), ['/client/employees/create','rt'=>'c'], ['class' => 'btn btn-primary', 'style' => 'float:right; ',]) ?>
</h1>
<?=
GridView::widget([
    'dataProvider' => new ActiveDataProvider([
        'query' => $model->getEmployees(),
    ]),
    'columns' => [
        'username',
//         'first_name',
//         'middle_name',
//         'last_name',
         'phone',
         'phone_mobile',
         'email:email',
        [
            'attribute' => 'manager_type',
            'value' => function ($model) {
                return $model->getType();
            },
        ],
        [
            'attribute' => 'status',
            'value' => function ($model) {
                return $model->getStatus();
            },
        ],
        ['class' => 'yii\grid\ActionColumn',
            'template'=>'{update} {delete}',
            'urlCreator'=>function( $action, $model, $key, $index) {
//                $params = ['id'=>$model->id];
//                $params[0] = $action.'-manager';
                $params = ['/client/employees/'.$action,'id'=>$model->id,'rt'=>'c'];

                return Url::toRoute($params);
            },
            'buttons'=>[
                'delete'=> function ($url, $model, $key) {
                    $a =  Html::a(Yii::t('buttons', 'Delete'), ['delete', 'id' => $model->id], [
                        'class' => 'btn btn-danger btn-grid-action-column',
                        'data' => [
                            'confirm' => Yii::t('titles', 'Are you sure you want to delete this item?'),
                            'method' => 'post',
                        ],
                    ]);
                    return $model->manager_type != $model::TYPE_BASE_ACCOUNT ? $a : '';
                },
                'update'=> function ($url, $model, $key) {
                    return $model->manager_type != $model::TYPE_BASE_ACCOUNT ? Html::a(Yii::t('buttons','Edit'), $url,['class'=>'btn btn-warning btn-grid-action-column']) : '';
                },
//                'view'=> function ($url, $model, $key) {
//                    return $model->manager_type != $model::TYPE_BASE_ACCOUNT ? Html::a('View', $url,['class'=>'btn btn-primary']) : '';
//                },
            ]
        ],
    ],
]);
?>