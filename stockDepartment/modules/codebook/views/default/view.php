<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\modules\codebook\models\Codebook */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('titles', 'Codebook'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="codebook-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('buttons', 'Edit'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('buttons', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('titles', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'cod_prefix',
            'name',
            'count_cell',
            'barcode',
            [
                'attribute' => 'base_type',
                'value' => $model->getBaseTypeValue()
            ],
            [
                'attribute' => 'status',
                'value' => $model->getStatusValue()
            ],
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

</div>
