<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;

/* @var $this yii\web\View */
/* @var $model common\modules\client\models\Client */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('forms', 'Clients'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="client-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('buttons', 'Edit'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('buttons', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('forms', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a(Yii::t('buttons', 'View client settings'), ['client-settings/index', 'client_id' => $model->id], ['class' => 'btn btn-success pull-right']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
//            'id',
//            'user_id',
            'legal_company_name',
            'username',
            'title',
            'first_name',
            'middle_name',
            'last_name',
            'phone',
            'phone_mobile',
            'email:email',
            'status',
            [
//                'displayOnly' => true,
//                'type' => DetailView::INPUT_DROPDOWN_LIST,
//                'items' => TlCars::getCarArray(),
                'attribute' => 'on_stock',
                'value' => $model->getOnStockValue(),

            ],
            'updatedUser.username',
            'createdUser.username',
//            [
//              'attribute' => 'updated_user_id',
//              'value' => $model->updatedUser->username,
//            ],
//            [
//              'attribute' => 'created_user_id',
//              'value' => $model->createdUser->username,
//            ],
            'created_at:datetime',
            'updated_at:datetime',
//            'deleted',
        ],
    ]) ?>

</div>

<h1 id="title-cars">
    <?= Html::encode(Yii::t('titles','Менеджеры компании')) ?>
    <?= Html::a(Yii::t('titles', 'Добавить менеджера'), ['/client/employees/create', 'client_id' => $model->id], ['class' => 'btn btn-primary', 'style' => 'float:right; ',]) ?>
</h1>
<?php //$q = $model->getManagers(); ?>
<?php //$q->where(['']); ?>
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

//                $params[0] = $action.'-manager';
//                $url = '-';

                    $params = ['id'=>$model->id];
                    $params[0] = '/client/employees/' . $action;
                    $url = Url::toRoute($params);
//                }

                return $url;
            },
            'buttons'=>[
                'delete'=> function ($url, $model, $key) {
                    $a =  Html::a(Yii::t('forms', 'Delete'), $url, [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => Yii::t('forms', 'Are you sure you want to delete this item?'),
                            'method' => 'post',
                        ],
                    ]);
                    return $model->manager_type != $model::TYPE_BASE_ACCOUNT ? $a : '';
                },
//                'delete'=> function ($url, $model, $key) {
//                    return $model->manager_type != $model::TYPE_BASE_ACCOUNT ? Html::a('Delete', $url,['class'=>'btn btn-danger']) : '';
//                },
                'update'=> function ($url, $model, $key) {
                    return $model->manager_type != $model::TYPE_BASE_ACCOUNT ? Html::a('Update', $url,['class'=>'btn btn-primary']) : '';
                },
            ]
        ],
    ],
]);
?>