<?php

use yii\helpers\Html;
use app\modules\transportLogistics\transportLogistics;


/* @var $this yii\web\View */
/* @var $model common\modules\transportLogistics\models\TlDeliveryProposal */

$this->title = Yii::t('transportLogistics/titles', 'Create DeliveryProposal');
$this->params['breadcrumbs'][] = ['label' => Yii::t('transportLogistics/titles', 'Tl Delivery Proposals'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tl-delivery-proposal-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'storeArray' => $storeArray,
    ]) ?>

</div>
