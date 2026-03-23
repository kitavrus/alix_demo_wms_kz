<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\modules\client\models\Client */

$this->title = Yii::t('forms', 'Create {modelClass}', [
    'modelClass' => 'Client',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('forms', 'Clients'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
