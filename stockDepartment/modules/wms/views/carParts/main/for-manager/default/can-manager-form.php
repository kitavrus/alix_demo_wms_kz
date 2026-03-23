<?php

use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $canManagerForm common\clientObject\main\forms\CanManagerForm */
?>

<div>
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($canManagerForm, 'code',[
        'errorOptions' => [
            'encode' => false,
            'class' => 'help-block'
            ]
         ])->textInput() ?>
    <?php ActiveForm::end(); ?>
</div>