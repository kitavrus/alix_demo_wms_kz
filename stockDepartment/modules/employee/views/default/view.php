<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\modules\employees\models\Employees */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('titles', 'Employees'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="employees-view">

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
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'user_id',
            'username',
            //'password',
           // 'title',
            'first_name',
            'middle_name',
            'last_name',
            'barcode',
            'phone',
            'phone_mobile',
            'email:email',
            [
                'attribute' => 'manager_type',
                'value' => $model->getType(),
            ],
            //'department',
            [
                'attribute' => 'status',
                'value' => $model->getStatus(),
            ],
            'createdUser.username',
            'updatedUser.username',
            [
                'attribute' => 'created_at',
                'format' => 'datetime'
            ],
            [
                'attribute' => 'updated_at',
                'format' => 'datetime'
            ],
            //'deleted',
        ],
    ]) ?>
</div>