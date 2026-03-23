<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\stock\models\StockZone */

$this->title = Yii::t('app', 'Create Stock Zone');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Stock Zones'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="stock-zone-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
