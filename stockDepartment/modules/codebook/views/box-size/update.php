<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\codebook\models\BoxSize */

$this->title = Yii::t('forms', 'Update {modelClass}: ', [
    'modelClass' => 'Box Size',
]) . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('forms', 'Box Sizes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('forms', 'Update');
?>
<div class="box-size-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
