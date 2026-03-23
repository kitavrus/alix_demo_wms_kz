<?php

use yii\helpers\Html;
use app\modules\city\city;


/* @var $this yii\web\View */
/* @var $model common\modules\city\models\Region */

$this->title = city::t('titles', 'Create region');
$this->params['breadcrumbs'][] = ['label' => city::t('titles', 'Regions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="region-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
