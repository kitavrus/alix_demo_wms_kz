<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\placementUnit\models\PlacementUnit */

$this->title = Yii::t('app', 'Create Placement Unit');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Placement Units'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="placement-unit-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
