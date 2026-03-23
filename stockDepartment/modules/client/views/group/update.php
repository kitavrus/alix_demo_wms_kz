<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\client\models\ClientGroup */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Client Group',
]) . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Client Groups'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="client-group-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
