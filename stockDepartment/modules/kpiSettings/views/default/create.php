<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\modules\kpiSettings\models\KpiSetting */

$this->title = Yii::t('titles', 'Create Setting');
$this->params['breadcrumbs'][] = ['label' => Yii::t('titles', 'Kpi Settings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="kpi-setting-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
