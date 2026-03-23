<?php

use yii\helpers\Html;
use app\modules\transportLogistics\transportLogistics;


/* @var $this yii\web\View */
/* @var $model common\modules\city\models\City */

$this->title = Yii::t('transportLogistics/titles', 'Create City');
$this->params['breadcrumbs'][] = ['label' => Yii::t('transportLogistics/titles', 'Cities'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="city-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
