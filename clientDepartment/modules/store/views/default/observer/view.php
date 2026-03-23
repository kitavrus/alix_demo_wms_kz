<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;

/* @var $this yii\web\View */
/* @var $model common\modules\store\models\Store */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('titles', 'Stores'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="store-view">

    <h1><?= Html::encode($this->title) ?></h1>

<!--    <p>-->
<!--        --><?//= Html::a(Yii::t('buttons', 'Edit'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
<!--        --><?//= Html::a(Yii::t('buttons', 'Delete'), ['delete', 'id' => $model->id], [
//            'class' => 'btn btn-danger',
//            'data' => [
//                'confirm' => Yii::t('titles', 'Are you sure you want to delete this item?'),
//                'method' => 'post',
//            ],
//        ]) ?>
<!--    </p>-->

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
//            'id',
            'name',
            'shopping_center_name',
            'shopping_center_name_lat',
            'shop_code',
//            'contact_first_name',
//            'contact_middle_name',
//            'contact_last_name',
//            'contact_first_name2',
//            'contact_middle_name2',
//            'contact_last_name2',
            'email:email',
            'phone',
            'phone_mobile',
//            'title',
            'description:ntext',
//            'address_type',
            [
                'attribute' => 'status',
                'value' => $model->getStatus()
            ],
            'country.name',
            'region.name',
            'city.name',
            'city_lat',
            'zip_code',
            'street',
            'house',

//            'comment:ntext',
//            'shop_code',
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

</div>


<h1 id="title-cars">
    <?= Html::encode(Yii::t('titles','Store managers')) ?>
<!--    --><?//= Html::a(Yii::t('titles', 'Create Client Manager'), ['/client/employees/create', 'store_id' => $model->id,'rt'=>'store'], ['class' => 'btn btn-primary', 'style' => 'float:right; ',]) ?>
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
            'template'=>'{view}',
            'urlCreator' => function ($action, $model, $key, $index) {

                $params = ['/client/employees/'.$action,'id' => $model->id,'rt'=>'store'];

                return Url::toRoute($params);
            },
            'buttons'=>[
//                'delete'=> function ($url, $model, $key) {
//                    $a =  Html::a(Yii::t('buttons', 'Delete'), $url, [
//                        'class' => 'btn btn-danger',
//                        'data' => [
//                            'confirm' => Yii::t('titles', 'Are you sure you want to delete this item?'),
//                            'method' => 'post',
//                        ],
//                    ]);
//                    return $model->manager_type != $model::TYPE_BASE_ACCOUNT ? $a : '';
//                },
//                'update'=> function ($url, $model, $key) {
//                    return $model->manager_type != $model::TYPE_BASE_ACCOUNT ? Html::a(Yii::t('buttons','Edit'), $url,['class'=>'btn btn-warning']) : '';
//                },
                'view'=> function ($url, $model, $key) {
                    return $model->manager_type != $model::TYPE_BASE_ACCOUNT ? Html::a(Yii::t('buttons','Edit'), $url,['class'=>'btn btn-primary']) : '';
                },
            ]
        ],
    ],
]);
?>
