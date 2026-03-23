<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\stock\models\StockZone */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Stock Zone',
]) . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Stock Zones'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="stock-zone-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>