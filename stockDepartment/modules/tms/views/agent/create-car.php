<?php

use yii\helpers\Html;
use app\modules\transportLogistics\transportLogistics;


/* @var $this yii\web\View */
/* @var $model common\modules\transportLogistics\models\TlCars */

$this->title = Yii::t('transportLogistics/titles', 'Create Car');
$this->params['breadcrumbs'][] = ['label' => Yii::t('transportLogistics/titles', 'Tl Cars'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tl-cars-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form-car', [
        'model' => $model,
    ]) ?>

</div>
