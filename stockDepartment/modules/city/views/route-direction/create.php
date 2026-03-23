<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\modules\city\models\RouteDirections */

$this->title = Yii::t('app', 'Создать направления');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Направления'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="route-directions-create">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>