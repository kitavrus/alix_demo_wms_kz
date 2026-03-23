<?php
/**
 * Created by PhpStorm.
 * User: Kitavrus
 * Date: 27.05.16
 * Time: 21:35
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\widgets\Select2;

/* @var $this yii\web\View */
/* @var $model app\modules\operatorDella\models\QuickMakeOrderFrom */
?>
<? $form = ActiveForm::begin([
    'id' => 'quick-make-order-from',
    'enableClientValidation' => true,
    'validateOnType' => true,
]); ?>
    <h3>Клиент</h3>
    <div class="row">
        <div class="col-md-2">
            <?= $form->field($model, 'companyName')
                ->textInput(['placeholder' => 'Название компании']); ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'companyPhone')
                ->textInput(['placeholder'])
                ->widget(\yii\widgets\MaskedInput::className(), [
                    'mask' => '+7 (999) 999 99 99']); ?>

        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'companyEmail')
                ->textInput(['placeholder' => 'Введите Эл.почту']); ?>
        </div>
    </div>
    <h3>Направление городов</h3>
    <h4>
        <small>Откуда</small>
    </h4>
    <div class="row">
        <div class="col-md-2">
            <?= $form->field($model, 'fromCity')->widget(Select2::classname(), [
                'language' => 'ru',
                'data' => $cityArray,
                'options' => ['placeholder' => 'Введите город'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'fromStreet')
                ->textInput(['placeholder' => 'Введите улицу']); ?>
        </div>
        <div class="col-md-1">
            <?= $form->field($model, 'fromHouse')
                ->textInput(['placeholder' => 'Дом №']); ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'fromAddressComment')
                ->textInput(['placeholder' => 'Комментарий к адресу']); ?>
        </div>
    </div>
    <h4>
        <small>Куда</small>
    </h4>
    <div class="row">
        <div class="col-md-2">
            <?= $form->field($model, 'toCity')->widget(Select2::classname(), [
                'language' => 'ru',
                'data' => $cityArray,
                'options' => ['placeholder' => 'Введите город'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'toStreet')
                ->textInput(['placeholder' => 'Введите улицу']); ?>
        </div>
        <div class="col-md-1">
            <?= $form->field($model, 'toHouse')
                ->textInput(['placeholder' => 'Дом №']); ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'toAddressComment')
                ->textInput(['placeholder' => 'Комментарий к адресу']); ?>
        </div>
    </div>
    <h3>Данные отправителя</h3>
    <div class="row">
        <div class="col-md-2">
            <?= $form->field($model, 'fromFirstName')
                ->textInput(['placeholder' => 'Имя отправителя']); ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'fromLastName')
                ->textInput(['placeholder' => 'Фамилия отправителя']); ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'fromMiddleName')
                ->textInput(['placeholder' => 'Очество отправителя']); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-2">
            <?= $form->field($model, 'fromPhoneOne')
                ->textInput(['placeholder'])
                ->widget(\yii\widgets\MaskedInput::className(), [
                    'mask' => '+7 (999) 999 99 99']); ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'fromPhoneTwo')
                ->textInput(['placeholder'])
                ->widget(\yii\widgets\MaskedInput::className(), [
                    'mask' => '+7 (999) 999 99 99']); ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'fromEmail')
                ->textInput(['placeholder' => 'Введите Эл.почту']); ?>
        </div>
    </div>
    <h3>Данные получателя</h3>
    <div class="row">
        <div class="col-md-2">
            <?= $form->field($model, 'toFirstName')
                ->textInput(['placeholder' => 'Имя отправителя']); ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'toLastName')
                ->textInput(['placeholder' => 'Фамилия отправителя']); ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'toMiddleName')
                ->textInput(['placeholder' => 'Очество отправителя']); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-2">
            <?= $form->field($model, 'toPhoneOne')
                ->textInput(['placeholder'])
                ->widget(\yii\widgets\MaskedInput::className(), [
                    'mask' => '+7 (999) 999 99 99']); ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'toPhoneTwo')
                ->textInput(['placeholder'])
                ->widget(\yii\widgets\MaskedInput::className(), [
                    'mask' => '+7 (999) 999 99 99']); ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'toEmail')
                ->textInput(['placeholder' => 'Введите Эл.почту']); ?>
        </div>
    </div>
    <h3>Данные груза</h3>
    <div class="row">
        <div class="col-md-2">
            <?= $form->field($model, 'kg')
                ->textInput(['placeholder' => 'kg']); ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'm3')
                ->textInput(['placeholder' => 'м3']); ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'placeQty')
                ->textInput(['placeholder' => 'Кол-во мест']); ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'cargoComment')
                ->textInput(['placeholder' => 'Введите название груза']); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-2">
            <?= $form->field($model, 'whoPays')
                ->dropDownList($model->getTransportWhoPaysArray(), ['placeholder' => 'Введите кто оплачивает']); ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'typeLoading')
                ->dropDownList($model->getTransportTypeLoadingArray(), ['placeholder' => 'Тип Погрузки']); ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'deliveryType')
                ->dropDownList($model->getDeliveryTypeArray(), ['placeholder' => 'Тип Доставки']); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'price')
                ->textInput(['placeholder' => 'Стоимость доставки']); ?>
        </div>
    </div>
    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
    </div>
<?php ActiveForm::end() ?>