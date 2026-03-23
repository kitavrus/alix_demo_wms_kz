<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\stock\models\Stock */

$this->title = Yii::t('froms', 'Update {modelClass}: ', [
    'modelClass' => 'Stock',
]) . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('froms', 'Stocks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('froms', 'Update');
?>
<div class="stock-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
