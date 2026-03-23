<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\modules\client\models\Client;
use kartik\select2\Select2;
use common\modules\transportLogistics\components\TLHelper;

/* @var $this yii\web\View */
/* @var $model common\modules\transportLogistics\models\TlDeliveryProposalDefaultRoute */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tl-delivery-proposal-default-route-form">

    <?php $form = ActiveForm::begin(); ?>

    <?//= $form->field($model, 'client_id')->dropDownList(ArrayHelper::map(Client::findAll(['status' => Client::STATUS_ACTIVE]), 'id', 'title'),['prompt' =>Yii::t('transportLogistics/titles', 'Select client')]); ?>

    <?=
    $form->field($model, 'from_point_id')->widget(Select2::classname(), [
        'language' => 'ru',
        'data' => TLHelper::getStockPointArray(),
        'options' => ['placeholder' => Yii::t('transportLogistics/titles', 'Select route from')],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]);
    ?>

    <?=
    $form->field($model, 'to_point_id')->widget(Select2::classname(), [
        'language' => 'ru',
        'data' => TLHelper::getStockPointArray(),
        'options' => ['placeholder' => Yii::t('transportLogistics/titles', 'Select route from')],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]);
    ?>

<!--    --><?//= $form->field($model, 'created_user_id')->textInput() ?>
<!---->
<!--    --><?//= $form->field($model, 'updated_user_id')->textInput() ?>
<!---->
<!--    --><?//= $form->field($model, 'created_at')->textInput() ?>
<!---->
<!--    --><?//= $form->field($model, 'updated_at')->textInput() ?>
<!---->
<!--    --><?//= $form->field($model, 'deleted')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('buttons', 'Create') : Yii::t('buttons', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
