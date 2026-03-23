<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\codebook\models\Codebook */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="codebook-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'cod_prefix')->textInput(['maxlength' => 3]) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 128]) ?>

    <?= $form->field($model, 'count_cell')->textInput() ?>

<!--    --><?//= $form->field($model, 'barcode')->textInput() ?>

    <?= $form->field($model, 'status')->dropDownList($model::getStatusArray()) ?>

    <?= $form->field($model, 'base_type')->dropDownList($model::getBaseTypeArray()) ?>

<!--    --><?//= $form->field($model, 'created_user_id')->textInput() ?>

<!--    --><?//= $form->field($model, 'updated_user_id')->textInput() ?>

<!--    --><?//= $form->field($model, 'created_at')->textInput() ?><!--/-->

<!--    --><?//= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('buttons', 'Create') : Yii::t('buttons', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
