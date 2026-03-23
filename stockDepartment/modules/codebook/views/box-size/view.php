<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\modules\codebook\models\BoxSize */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('forms', 'Box Sizes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box-size-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('forms', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('forms', 'Delete'), ['delete', 'id' => $model->id], [
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
//            'id',
            'box_height',
            'box_width',
            'box_length',
            'box_code',
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
