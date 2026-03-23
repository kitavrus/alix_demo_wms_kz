<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\modules\stock\models\Inventory */

$this->title = Yii::t('inventory/forms', 'Create Inventory');
$this->params['breadcrumbs'][] = ['label' => Yii::t('inventory/forms', 'Inventories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="inventory-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'clientsArray' => $clientsArray,
    ]) ?>

</div>
