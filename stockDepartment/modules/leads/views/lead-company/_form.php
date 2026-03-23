<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\modules\leads\models\TtCompanyLead;

/* @var $this yii\web\View */
/* @var $model common\modules\leads\models\TtCompanyLead */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tt-company-lead-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'customer_name')->textInput(['maxlength' => 128]) ?>

    <?= $form->field($model, 'customer_company_name')->textInput(['maxlength' => 128]) ?>

    <?= $form->field($model, 'customer_position')->textInput(['maxlength' => 128]) ?>

    <?= $form->field($model, 'customer_phone')->textInput(['maxlength' => 128]) ?>

    <?= $form->field($model, 'customer_email')->textInput(['maxlength' => 128]) ?>

    <?= $form->field($model, 'status')->dropDownList($model->getStatusArray()) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('buttons', 'Create') : Yii::t('buttons', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
