<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\transportLogistics\transportLogistics;

/* @var $this yii\web\View */
/* @var $model stockDepartment\modules\transportLogistics\models\TlAgentsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tl-agents-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'title') ?>

    <?= $form->field($model, 'phone') ?>

    <?= $form->field($model, 'phone_mobile') ?>

    <?php // echo $form->field($model, 'description') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'contact_first_name') ?>

    <?php // echo $form->field($model, 'contact_middle_name') ?>

    <?php // echo $form->field($model, 'contact_last_name') ?>

    <?php // echo $form->field($model, 'contact_phone') ?>

    <?php // echo $form->field($model, 'contact_phone_mobile') ?>

    <?php // echo $form->field($model, 'contact_first_name2') ?>

    <?php // echo $form->field($model, 'contact_middle_name2') ?>

    <?php // echo $form->field($model, 'contact_last_name2') ?>

    <?php // echo $form->field($model, 'contact_phone2') ?>

    <?php // echo $form->field($model, 'contact_phone_mobile2') ?>

    <?php // echo $form->field($model, 'address_title') ?>

    <?php // echo $form->field($model, 'country') ?>

    <?php // echo $form->field($model, 'region') ?>

    <?php // echo $form->field($model, 'city') ?>

    <?php // echo $form->field($model, 'zip_code') ?>

    <?php // echo $form->field($model, 'street') ?>

    <?php // echo $form->field($model, 'house') ?>

    <?php // echo $form->field($model, 'entrance') ?>

    <?php // echo $form->field($model, 'flat') ?>

    <?php // echo $form->field($model, 'intercom') ?>

    <?php // echo $form->field($model, 'floor') ?>

    <?php // echo $form->field($model, 'comment') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('transportLogistics/buttons', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('transportLogistics/buttons', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
