<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model clientDepartment\modules\client\models\ClientEmployeesSearch */

$this->title = $model->username;
$this->params['breadcrumbs'][] = ['label' => Yii::t('titles', 'Employees'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-managers-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('buttons', 'Edit'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
<!--        --><?//= Html::a(Yii::t('forms', 'Delete'), ['delete', 'id' => $model->id], [
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
//            'id',
            'store.name',
    [
        'attribute'=>'store_id',
        'value'=>$model->getStoreTitle()
    ],
//            'client_id',
//            'user_id',
            'username',
            'first_name',
            'middle_name',
            'last_name',
            'phone',
            'phone_mobile',
            'email:email',
            [
                'attribute' => 'manager_type',
                'value' =>  $model->getType()
            ],
            [
                'attribute' => 'status',
                'value' =>  $model->getStatus()
            ],
//            'createdUser.username',
//            'updatedUser.username',
            [
                'attribute' => 'created_user_id',
                'value' => $model::getUserName($model->created_user_id),
            ],
            [
                'attribute' => 'updated_user_id',
                'value' => $model::getUserName($model->updated_user_id),
            ],
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

</div>
