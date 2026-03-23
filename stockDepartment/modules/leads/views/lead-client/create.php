<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\modules\leads\models\ExternalClientLead */

$this->title = Yii::t('lead/forms', 'Create {modelClass}', [
    'modelClass' => 'External Client Lead',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead/forms', 'External Client Leads'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="external-client-lead-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
