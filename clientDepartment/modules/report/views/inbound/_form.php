<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\inbound\models\InboundOrder */
/* @var $form yii\widgets\ActiveForm */

//\yii\helpers\VarDumper::dump($model->order_number,10,true);
//die;
?>

<div class="store-form">
    <?php $form = ActiveForm::begin(); ?>
    <?php echo $form->field($model, 'id')->hiddenInput()->label(false); ?>
    <?php echo $form->field($model, 'comments')->textarea(['rows' => 6]) ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('buttons', 'Create') : Yii::t('buttons', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
