<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\transportLogistics\models\TlOutboundRegistry */

$this->title = Yii::t('transportLogistics/titles', 'Update Outbound Registry:') . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('transportLogistics/titles', 'Tl: Outbound Registries'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('transportLogistics/titles', 'Update');
?>
<div class="tl-outbound-registry-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
