<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\leads\models\TtCompanyLead */

$this->title = Yii::t('leads/titles', 'Update company lead') . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('leads/titles', 'Company lead'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('titles', 'Update');
?>
<div class="tt-company-lead-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
