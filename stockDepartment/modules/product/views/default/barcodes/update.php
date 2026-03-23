<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\product\models\Product */

$this->title = Yii::t('titles', 'Update Product: ') . ' ' . $model->barcode;
$this->params['breadcrumbs'][] = ['label' => Yii::t('titles', 'Products'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->barcode, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('titles', 'Update');
?>
<div class="product-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
