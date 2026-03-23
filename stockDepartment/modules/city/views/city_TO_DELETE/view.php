<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\modules\transportLogistics\transportLogistics;


/* @var $this yii\web\View */
/* @var $model common\modules\city\models\City */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('transportLogistics/titles', 'Cities'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="city-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('transportLogistics/buttons', 'Edit'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('transportLogistics/buttons', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('transportLogistics/forms', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
//            'id',
            'name',
            'region.name',
            'comment:ntext',
            'createdUser.username',
            'updatedUser.username',
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

</div>
