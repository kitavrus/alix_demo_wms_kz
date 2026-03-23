<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 03.12.14
 * Time: 14:42
 */

use common\modules\transportLogistics\components\TLHelper;
use common\modules\transportLogistics\models\TlCars;
use common\modules\transportLogistics\models\TlAgents;
use common\modules\client\models\Client;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use app\modules\transportLogistics\transportLogistics;
use kartik\datecontrol\DateControl;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;

/* @var $this yii\web\View
/* @var $model stockDepartment\modules\transportLogistics\models\CarModelPopup */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tl-delivery-proposal-car-model-popup-form">
    <h2 align="center">Информация об авто</h2>
    <?php $form = ActiveForm::begin([
        'id'=>'car-model-popup-form',
        'enableClientValidation' => true,
        'validateOnType' => true,
        'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL],
        'type' => ActiveForm::TYPE_HORIZONTAL,
    ]); ?>

    <?= $form->field($model, 'agent_id')->widget(Select2::classname(), [
        'language' => 'ru',
        'data' => TlAgents::getActiveAgentsArray(),
        'options' => ['placeholder' => Yii::t('transportLogistics/forms','Please select the shipping company')],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]);
    ?>

    <?= $form->field($model, 'car_id')->dropDownList( [] ,['prompt'=>'']); ?>

    <?= $form->field($model, 'driver_name')->textInput() ?>
    <?= $form->field($model, 'driver_phone')->textInput() ?>
    <?= $form->field($model, 'driver_auto_number')->textInput() ?>

    <?= $form->field($model, 'car_price_invoice')->textInput(); ?>
    <?= $form->field($model, 'car_price_invoice_with_vat')->textInput(['maxlength' => 26]); ?>

    <h2 align="center">Расходы</h2>
    <?= $form->field($model, 'ue_name')->textInput(['maxlength' => 255]) ?>
    <?= $form->field($model, 'ue_price_cache')->textInput(['maxlength' => 26]) ?>
    <?= $form->field($model, 'ue_cash_no')->dropDownList(\common\models\ActiveRecord::getPaymentMethodArray()) ?>
    <?= $form->field($model, 'ue_who_pays')->dropDownList(\common\modules\transportLogistics\models\TlDeliveryProposalRouteUnforeseenExpenses::getWhoPaysArray()) ?>
    <?= $form->field($model, 'ue_comment')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton( Yii::t('transportLogistics/buttons', 'Create Car'), ['class' =>'btn btn-success col-sm-offset-4']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>