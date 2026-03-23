<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\leads\models\ExternalClientLead */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="external-client-lead-form col-md-8">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'status')->dropDownList($model->getClientStatusArray()) ?>

    <?= $form->field($model, 'full_name')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'phone')->textInput(['maxlength' => 255]) ?>

    <?//= $form->field($model, 'client_email')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'client_type')->dropDownList($model->getClientTypeArray()) ?>

    <?= $form->field($model, 'legal_company_name')->textInput(['maxlength' => 255]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('buttons', 'Create') : Yii::t('buttons', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
