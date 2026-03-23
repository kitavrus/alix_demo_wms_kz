<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\city\models\RouteDirections */

$this->title = Yii::t('app', 'Направления обновить') . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Направления'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Изменить');
?>
<div class="route-directions-update">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>