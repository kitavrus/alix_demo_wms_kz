<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\modules\transportLogistics\models\TlAgents;
use app\modules\transportLogistics\transportLogistics;

/* @var $this yii\web\View */
/* @var $model common\modules\transportLogistics\models\TlCars */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tl-cars-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'agent_id')->dropDownList(TlAgents::getActiveAgentsArray()); ?>

<!--    --><?//= $form->field($model, 'title')->textInput(['maxlength' => 128]) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 128]) ?>


    <?= $form->field($model, 'mc')->textInput(['maxlength' => 26]) ?>

    <?= $form->field($model, 'kg')->textInput(['maxlength' => 26]) ?>

    <?= $form->field($model, 'status')->dropDownList($model::getStatusArray()); ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>


    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('transportLogistics/buttons', 'Create') : Yii::t('transportLogistics/buttons', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
