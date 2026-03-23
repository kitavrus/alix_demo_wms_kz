<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\city\models\RouteDirections */

$this->title = Yii::t('app', 'Направления обновить') . $model->our_box_to_stock_barcode. ' '.$model->client_box_barcode;
$this->params['breadcrumbs'][] = Yii::t('app', 'Изменить');
?>
<div class="route-directions-update">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>