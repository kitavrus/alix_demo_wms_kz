<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\client\models\ClientEmployees */
/* @var $form yii\widgets\ActiveForm */
?>

<?php $form = ActiveForm::begin(); ?>
<?= $form->field($model, 'phone')->textInput(['maxlength' => 64]) ?>
<?= $form->field($model, 'phone_mobile')->textInput(['maxlength' => 64]) ?>
<?= Html::hiddenInput('rt', Yii::$app->request->get('rt', 0)) ?>
<div class="form-group">
    <?= Html::submitButton($model->isNewRecord ? Yii::t('buttons', 'Create') : Yii::t('buttons', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
</div>
<?php ActiveForm::end(); ?>
