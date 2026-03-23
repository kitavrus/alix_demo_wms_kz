<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\modules\city\city;

/* @var $this yii\web\View */
/* @var $model common\modules\city\models\Country */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => city::t('titles', 'Countries'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="country-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(city::t('buttons', 'Edit'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(city::t('buttons', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => city::t('forms', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'comment:ntext',
            'createdUser.username',
            'updatedUser.username',
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

</div>
