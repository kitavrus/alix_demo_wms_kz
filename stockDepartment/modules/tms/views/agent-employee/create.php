<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\modules\transportLogistics\models\TlAgentEmployees */

$this->title = Yii::t('forms', 'Create {modelClass}', [
    'modelClass' => 'Tl Agent Employees',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('forms', 'Tl Agent Employees'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tl-agent-employees-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
