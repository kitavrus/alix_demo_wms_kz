<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\placementUnit\models\PlacementUnit */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Placement Unit',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Placement Units'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="placement-unit-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
