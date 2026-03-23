<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model stockDepartment\modules\sheetShipment\models\SheepShipmentPlaceAddressAR */

$this->title = Yii::t('app', 'Create Sheep Shipment Place Address');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Sheep Shipment Place Address'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sheep-shipment-place-address-ar-create">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
