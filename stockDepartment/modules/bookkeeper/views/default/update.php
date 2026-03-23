<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model stockDepartment\modules\Bookkeeper\models\Bookkeeper */

$this->title = ($model->type_id == $model::TYPE_PLUS ? Yii::t('app', 'Изменить ПРИХОД') : Yii::t('app', 'Изменить РАСХОД') );
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Учет'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Изменить');
?>
<div class="bookkeeper-update">
<!--    <h1>--><?php //echo Html::encode($this->title) ?><!--</h1>-->

    <?php if($model->status == $model::STATUS_DONE) { ?>
        <h1 class="">Пожалуйста будьте внимательны!!! Этот расход уже закрыт. Если будете вносить изменения не забудте изменить статус но "новый"</h1>
    <?php } else { ?>
        <h1><?= Html::encode($this->title) ?></h1>
    <?php } ?>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
