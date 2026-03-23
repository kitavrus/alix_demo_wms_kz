<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\ecommerce\entities\EcommerceCheckBoxInventory */

$this->title = Yii::t('app', 'Create B2B Check Box Inventory');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'B2B Check Box Inventories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ecommerce-check-box-inventory-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
