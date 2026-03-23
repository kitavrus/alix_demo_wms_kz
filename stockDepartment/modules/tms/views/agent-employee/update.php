<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\transportLogistics\models\TlAgentEmployees */

$this->title = Yii::t('forms', 'Update {modelClass}: ', [
    'modelClass' => 'Tl Agent Employees',
]) . ' ' . $model->username;
$this->params['breadcrumbs'][] = ['label' => Yii::t('forms', 'Tl Agent Employees'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->username, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('forms', 'Update');
?>
<div class="tl-agent-employees-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
