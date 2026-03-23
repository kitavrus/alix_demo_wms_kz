<?php

use yii\helpers\Html;
use app\modules\transportLogistics\transportLogistics;

/* @var $this yii\web\View */
/* @var $model common\modules\transportLogistics\models\TlDeliveryProposal */

$this->title = Yii::t('transportLogistics/titles', 'Заявка на доставку: ');
$this->params['breadcrumbs'][] = ['label' => Yii::t('transportLogistics/titles', 'Tl Delivery Proposals'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('transportLogistics/titles', 'Update');
?>
<div class="tl-delivery-proposal-update">

    <h1><?= Html::encode($this->title).$model->id ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'storeArray' => $storeArray,
    ]) ?>

</div>
