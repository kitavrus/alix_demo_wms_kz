<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\client\models\ClientEmployees */

$this->title = Yii::t('forms', 'Update {modelClass}: ', [
    'modelClass' => 'Client Managers',
]) . ' ' . $model->username;
$this->params['breadcrumbs'][] = ['label' => Yii::t('forms', 'Client Managers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->username, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('forms', 'Update');
?>
<div class="client-managers-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'storeList' => $storeList,
    ]) ?>

</div>
