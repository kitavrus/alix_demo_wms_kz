<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\store\models\StoreReviews */

$this->title = Yii::t('forms', 'Update {modelClass}: ', [
    'modelClass' => 'Store Reviews',
]) . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('forms', 'Store Reviews'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('forms', 'Update');
?>
<div class="store-reviews-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
