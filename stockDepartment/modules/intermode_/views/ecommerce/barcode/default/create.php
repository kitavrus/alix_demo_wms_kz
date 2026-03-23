<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\ecommerce\entities\EcommerceBarcodeManager */

$this->title = 'Create Ecommerce Barcode Manager';
$this->params['breadcrumbs'][] = ['label' => 'Ecommerce Barcode Managers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ecommerce-barcode-manager-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
