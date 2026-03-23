<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\modules\client\models\ClientSettings */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('titles', 'Client Settings'), 'url' => ['index', 'client_id'=>$model->client_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-settings-view">

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
            'client.username',
            'option_name',
            'option_value',
            'default_value',
            'option_type',
            'description:ntext',
            'createdUser.username',
            'updatedUser.username',
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

</div>
