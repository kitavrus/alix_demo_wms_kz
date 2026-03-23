<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\ecommerce\entities\EcommerceCheckBoxInventory */

$this->title = Yii::t('app', 'Update B2B Check Box Inventory: {nameAttribute}', [
    'nameAttribute' => $model->id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'B2B Check Box Inventories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
//$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="ecommerce-check-box-inventory-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
