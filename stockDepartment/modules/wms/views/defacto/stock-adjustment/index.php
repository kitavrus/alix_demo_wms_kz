<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 28.03.2020
 * Time: 10:53
 */


use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;
use yii\helpers\Url;
use yii\helpers\Html;

//\app\modules\ecommerce\assets\defacto\ScanCancelFormAsset::register($this);

?>
<h1>Корректировка Запасов добавление товара (плюсы)</h1>
<div id="messages-scanning-container">
    <div id="messages-base-line"></div>
    <?= Alert::widget([
        'options' => [
            'id' => 'messages-scanning-list',
            'class' => 'alert-info hidden',
        ],
        'body' => '<span id="messages-scanning-list-body"></span>',
    ]);
    ?>
</div>

<div class="scanning-form">
    <?php $form = ActiveForm::begin([
            'id' => 'stockadjustmentForm',
            'enableClientValidation' => false,
            'validateOnChange' => false,
            'validateOnSubmit' => false,
        ]
    ); ?>

    <?= $form->field($model, 'productBarcode')->textInput([
        'class' => 'form-control input-lg',
        'data-url' => Url::toRoute('product-barcode')
    ]); ?>

    <?= $form->field($model, 'productQuantity')->textInput([
        'class' => 'form-control input-lg',
        'data-url' => Url::toRoute('product-quantity')
    ]); ?>

    <?= $form->field($model, 'addressBoxBarcode')->textInput([
        'class' => 'form-control input-lg',
        'data-url' => Url::toRoute('address-box-barcode')
    ]); ?>

    <?= $form->field($model, 'reason')->textInput([
        'class' => 'form-control input-lg',
        'data-url' => Url::toRoute('reason')
    ]); ?>

    <?= $form->field($model, 'productOperator')->dropDownList(['+'=>'Добавляем']); ?>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('outbound/buttons', 'Выполнить'), ['class' =>'btn btn-success']) ?>
    </div>
    <?php ActiveForm::end(); ?>
    <div id="error-container">
        <div id="error-base-line"></div>
        <?= Alert::widget([
            'options' => [
                'id' => 'error-list',
                'class' => 'alert-danger hidden',
            ],
            'body' => '',
        ]);
        ?>
    </div>
    <div id="show-picking-list-items" class="table-responsive"></div>
</div>