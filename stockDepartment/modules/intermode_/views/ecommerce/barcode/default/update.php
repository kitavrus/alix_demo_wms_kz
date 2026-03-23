<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\ecommerce\entities\EcommerceBarcodeManager */

$this->title = 'Update Ecommerce Barcode Manager: {nameAttribute}';
$this->params['breadcrumbs'][] = ['label' => 'Ecommerce Barcode Managers', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="ecommerce-barcode-manager-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
