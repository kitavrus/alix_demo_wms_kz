<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model clientDepartment\modules\client\models\ClientEmployeesSearch */

$this->title = "Редактирование профиля: ". $model->username;
$this->params['breadcrumbs'][] = ['label' => $model->username, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('titles', 'Update');
?>
<div class="client-managers-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
