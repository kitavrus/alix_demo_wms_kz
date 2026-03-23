<?php

use yii\helpers\Html;
use app\modules\transportLogistics\transportLogistics;

/* @var $this yii\web\View */
/* @var $model common\modules\transportLogistics\models\TlCars */

$this->title = Yii::t('transportLogistics/titles', 'Update Car: ') . ' ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('transportLogistics/titles', 'Tl Cars'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('transportLogistics/titles', 'Update');
?>
<div class="tl-cars-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form-car', [
        'model' => $model,
    ]) ?>

</div>
