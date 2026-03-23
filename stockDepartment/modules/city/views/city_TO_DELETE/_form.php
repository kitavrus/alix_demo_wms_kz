<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use common\modules\city\models\Region;
use app\modules\city\city;

/* @var $this yii\web\View */
/* @var $model common\modules\city\models\City */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="city-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 64]) ?>

<!--    --><?//= $form->field($model, 'region_id')->textInput() ?>
    <?= $form->field($model, 'region_id')->dropDownList(ArrayHelper::map(Region::find()->all(), 'id', 'name')); ?>

    <?= $form->field($model, 'comment')->textarea(['rows' => 6]) ?>

<!--    --><?//= $form->field($model, 'created_user_id')->textInput() ?>
<!---->
<!--    --><?//= $form->field($model, 'updated_user_id')->textInput() ?>
<!---->
<!--    --><?//= $form->field($model, 'created_at')->textInput() ?>
<!---->
<!--    --><?//= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('transportLogistics/buttons', 'Create') : Yii::t('transportLogistics/buttons', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
