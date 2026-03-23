<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\codebook\models\BoxSize */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="box-size-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'box_height')->textInput(['maxlength' => 4]) ?>

    <?= $form->field($model, 'box_width')->textInput(['maxlength' => 4]) ?>

    <?= $form->field($model, 'box_length')->textInput(['maxlength' => 4]) ?>

    <?= $form->field($model, 'box_code')->textInput(['maxlength' => 4]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('forms', 'Create') : Yii::t('forms', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
