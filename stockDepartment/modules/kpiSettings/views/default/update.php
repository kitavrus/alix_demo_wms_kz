<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\kpiSettings\models\KpiSetting */

$this->title = Yii::t('titles', 'Update Setting') . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('titles', 'Kpi Settings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('titles', 'Update');
?>
<div class="kpi-setting-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
