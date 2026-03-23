<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/**
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $client_name
 * @property string $client_phone
 * @property string $client_email
 * @property integer $client_type
 * @property string $company_name
 * @property integer $status
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted
 */
?>

<div class="store-form">
    <?php $form = ActiveForm::begin(); ?>
	<?= $form->field($model, 'first_name'); ?>
	<?= $form->field($model, 'middle_name'); ?>
	<?= $form->field($model, 'last_name'); ?>
    <?= $form->field($model, 'email')->textInput(['maxlength' => 128]) ?>
    <?= $form->field($model, 'password')->passwordInput() ?>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('client/buttons', 'Save changes'),['class' => 'btn btn-success']); ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>