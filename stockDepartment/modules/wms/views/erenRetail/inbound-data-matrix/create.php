<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\modules\dataMatrix\models\InboundDataMatrix */

$this->title = 'Create Inbound Data Matrix';
$this->params['breadcrumbs'][] = ['label' => 'Inbound Data Matrices', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="inbound-data-matrix-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
