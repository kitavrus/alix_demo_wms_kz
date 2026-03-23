<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model stockDepartment\modules\sheetShipment\models\SheepShipmentPlaceAddressAR */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Sheep Shipment Place Address',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Sheep Shipment Place Address'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('titles', 'Update');
?>
<div class="sheep-shipment-place-address-ar-update">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
