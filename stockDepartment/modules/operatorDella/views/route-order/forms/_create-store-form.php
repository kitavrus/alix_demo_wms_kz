<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 24.08.14
 * Time: 19:07
 */


use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use common\modules\transportLogistics\components\TLHelper;
use common\modules\client\models\Client;
use common\modules\store\models\Store;
use app\modules\transportLogistics\transportLogistics;

/* @var $this yii\web\View
 * @var $model common\modules\store\models\Store */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="store-form">

    <?php $form = ActiveForm::begin([
        'id' => 'add-new-route-form',
        'enableClientValidation' => true,
        'validateOnType' => true,
    ]); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 128]) ?>
    <?= $form->field($model, 'shopping_center_name')->textInput(['maxlength' => 128]) ?>
    <?= $form->field($model, 'type_use')->dropDownList(Store::getTypeUseArray(),['prompt'=>Yii::t('titles', 'Please select type')]) ?>
    <?= $form->field($model, 'email')->textInput(['maxlength' => 64]) ?>

    <?= $form->field($model, 'phone', [
        'template' => "{label}\n{input-group-begin}{counter}{input}{input-group-end}\n{hint}\n{error}\n",
        'parts' => [
            '{input-group-begin}' => '<div class="input-group">',
            '{input-group-end}' => '</div>',
            '{counter}' => '<div class="input-group-addon" >+7</div>',
        ]
    ])->widget(\yii\widgets\MaskedInput::className(), [
        'model' => $model,
        'mask' => '999-999-99-99',
    ]); ?>

    <?= $form->field($model, 'phone_mobile', [
        'template' => "{label}\n{input-group-begin}{counter}{input}{input-group-end}\n{hint}\n{error}\n",
        'parts' => [
            '{input-group-begin}' => '<div class="input-group">',
            '{input-group-end}' => '</div>',
            '{counter}' => '<div class="input-group-addon" >+7</div>',
        ]
    ])->widget(\yii\widgets\MaskedInput::className(), [
        'model' => $model,
        'mask' => '999-999-99-99',
    ]); ?>

    <?= $form->field($model, 'status')->dropDownList($model::getStatusArray()) ?>
    <?= $form->field($model, 'country_id')->dropDownList(TLHelper::getCountryArray(), ['prompt' =>Yii::t('transportLogistics/titles', 'Select country')]) ?>
    <?= $form->field($model, 'region_id')->dropDownList(TLHelper::getRegionArray(), ['prompt' =>Yii::t('transportLogistics/titles', 'Select region')]) ?>
    <?= $form->field($model, 'city_id')->dropDownList(TLHelper::getCityArray(), ['prompt' =>Yii::t('transportLogistics/titles', 'Select city')]) ?>
    <?= $form->field($model, 'street')->textInput(['maxlength' => 128]) ?>
    <?= $form->field($model, 'house')->textInput(['maxlength' => 6]) ?>
    <?= $form->field($model, 'floor')->textInput() ?>
    <?= $form->field($model, 'client_id')->hiddenInput()->label(false); ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('buttons', 'Create') : Yii::t('buttons', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
