<?php

use yii\helpers\Html;
use app\modules\city\city;

/* @var $this yii\web\View */
/* @var $model common\modules\city\models\Country */

$this->title = city::t('titles', 'Update Country: ', [
    'modelClass' => 'Country',
]) . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => city::t('titles', 'Countries'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = city::t('titles', 'Update');
?>
<div class="country-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
