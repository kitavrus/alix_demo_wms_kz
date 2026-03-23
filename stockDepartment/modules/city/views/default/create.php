<?php

use yii\helpers\Html;
use app\modules\city\city;


/* @var $this yii\web\View */
/* @var $model common\modules\city\models\City */

$this->title = city::t('titles', 'Create City');
$this->params['breadcrumbs'][] = ['label' => city::t('titles', 'Cities'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="city-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
