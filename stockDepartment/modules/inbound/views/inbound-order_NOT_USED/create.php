<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\modules\inbound\models\InboundOrder */

$this->title = Yii::t('forms', 'Create {modelClass}', [
    'modelClass' => 'Inbound Order',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('forms', 'Inbound Orders'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="inbound-order-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
