<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\stock\models\Inventory */

$this->title = Yii::t('inventory/forms', 'Inventory');
$this->params['breadcrumbs'][] = ['label' => Yii::t('inventory/forms', 'Inventory'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('inventory/forms', 'Update');
?>
<div class="inventory-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'clientsArray' => $clientsArray,
    ]) ?>

</div>
