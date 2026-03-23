<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\city\city;

/* @var $this yii\web\View */
/* @var $model stockDepartment\modules\transportLogistics\models\CountrySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="country-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'comment') ?>

    <?= $form->field($model, 'created_user_id') ?>

    <?= $form->field($model, 'updated_user_id') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton(city::t('buttons', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(city::t('buttons', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
