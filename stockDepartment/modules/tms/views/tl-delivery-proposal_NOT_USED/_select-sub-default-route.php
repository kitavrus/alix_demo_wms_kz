<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 25.09.2015
 * Time: 11:14
 */

use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

?>

<div class="select-sub-route-form">
    <?php $form = ActiveForm::begin([
        'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_MEDIUM],
        'id' => 'select-sub-route-model-popup-form',
        'enableClientValidation' => true,
        'validateOnType' => true,
        'action' => Url::toRoute('save-selected-default-route'),
    ]); ?>

    <?= $form->field($model, 'sub_default_route_id')->dropDownList($drArray,
        [
            'prompt' => 'Выберите маршрут по умолчанию',
            'data' => ['url' => Url::toRoute('get-grid-default-sub-routs')],
        ]) ?>
    <?= $form->field($model, 'delivery_proposal_id')->hiddenInput()->label(false); ?>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('transportLogistics/buttons', 'Update'), ['class' => 'btn btn-primary', 'id' => 'select-sub-route-submit-bt']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<div id="default-sub-route-grid"></div>