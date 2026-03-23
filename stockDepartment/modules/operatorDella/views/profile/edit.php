<?php

use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = Yii::t('client/titles', 'Editing profile');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
