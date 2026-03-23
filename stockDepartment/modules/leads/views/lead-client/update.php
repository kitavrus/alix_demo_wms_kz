<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\leads\models\ExternalClientLead */

$this->title = Yii::t('leads/titles', 'Update client') .' '. $model->full_name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('leads/titles', 'External Client Leads'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->full_name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('titles', 'Update');
?>
<div class="external-client-lead-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
