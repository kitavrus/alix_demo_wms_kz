<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\datecontrol\DateControl;

/* @var $this yii\web\View */
/* @var $model stockDepartment\modules\Bookkeeper\models\Bookkeeper */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="bookkeeper-form">

    <?php $form = ActiveForm::begin(); ?>

<!--    --><?php //echo $form->field($model, 'tl_delivery_proposal_id')->textInput() ?>
<!--    --><?php //echo $form->field($model, 'tl_delivery_proposal_route_unforeseen_expenses_id')->textInput() ?>

    <?= $form->field($model, 'name_supplier')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'department_id')->dropDownList($model->getDepartmentIdArray()) ?>
    <?= $form->field($model, 'doc_type_id')->dropDownList($model->getDocTypeIdArray()) ?>
<!--    --><?php //echo $form->field($model, 'doc_file')->fileInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'status')->dropDownList($model->getStatusArray()) ?>
    <?= $form->field($model, 'price')->textInput()->label(($model->type_id == $model::TYPE_PLUS ? Yii::t('app', 'ПРИХОД') : Yii::t('app', 'РАСХОД') )) ?>

    <?= $form->field($model, 'date_at')->widget(DateControl::className(), [
        'type'=>DateControl::FORMAT_DATETIME,
    ]); ?>

    <?= $form->field($model, 'type_id')->hiddenInput()->label(false); ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Создать') : Yii::t('app', 'Изменить'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
