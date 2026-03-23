<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\city\city;
use common\modules\city\models\Country;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\modules\city\models\Region */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="region-form">

    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'country_id')->dropDownList(ArrayHelper::map(Country::find()->all(), 'id', 'name')); ?>
    <?= $form->field($model, 'name')->textInput(['maxlength' => 64]) ?>
<!--    --><?//= $form->field($model, 'comment')->textarea(['rows' => 6]) ?>
<!--    --><?//= $form->field($model, 'updated_user_id')->textInput() ?>
<!--    --><?//= $form->field($model, 'created_at')->textInput() ?>
<!--    --><?//= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? city::t('buttons', 'Create') : city::t('buttons', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
