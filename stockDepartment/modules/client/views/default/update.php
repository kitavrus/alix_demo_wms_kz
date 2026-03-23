<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\client\models\Client */

$this->title = Yii::t('titles', 'Update Client: ') . ' ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('titles', 'Clients'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('titles', 'Update');
?>
<div class="client-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
