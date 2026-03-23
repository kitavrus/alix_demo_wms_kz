<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model stockDepartment\modules\Bookkeeper\models\Bookkeeper */
$this->title = ($model->type_id == $model::TYPE_PLUS ? Yii::t('app', 'Создать ПРИХОД') : Yii::t('app', 'Создать РАСХОД') );

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Учет'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bookkeeper-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
