<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\modules\client\models\Client;
use common\modules\store\models\Store;
use yii\helpers\ArrayHelper;
use kartik\datecontrol\DateControl;

/* @var $this yii\web\View */
/* @var $model common\modules\store\models\StoreReviews */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="store-reviews-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'client_id')->dropDownList( ArrayHelper::map(Client::find()->all(),'id','username')); ?>

    <?= $form->field($model, 'store_id')->dropDownList( ArrayHelper::map(Store::find()->all(),'id','name')); ?>

    <?= $form->field($model, 'tl_delivery_proposal_id')->textInput() ?>

    <?= $form->field($model, 'delivery_datetime')->widget(DateControl::className(), [
            'type'=>DateControl::FORMAT_DATETIME,

        ]); ?>

    <?= $form->field($model, 'rate')->textInput() ?>

    <?= $form->field($model, 'number_of_places')->textInput() ?>

    <?= $form->field($model, 'comment')->textarea(['maxlength' => 999, 'rows'=>'6']) ?>

<!--    --><?//= $form->field($model, 'created_user_id')->textInput() ?>
<!---->
<!--    --><?//= $form->field($model, 'updated_user_id')->textInput() ?>
<!---->
<!--    --><?//= $form->field($model, 'created_at')->textInput() ?>
<!---->
<!--    --><?//= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('buttons', 'Create') : Yii::t('buttons', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
