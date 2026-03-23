<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 02.02.15
 * Time: 14:58
 */
//use yii\helpers\Html;
//use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;
//use yii\helpers\Url;
//use yii\bootstrap\Modal;
?>
<div id="messages-container">
    <div id="messages-base-line"></div>
    <?= Alert::widget([
        'closeButton'=>false,
        'options' => [
            'id' => 'messages-list',
            'class' => 'alert-info hidden',
        ],
        'body' => '<span id="messages-list-body"></span>',
    ]);
    ?>
</div>
<div class="begin-end-pick-list-form">
<?php $form = ActiveForm::begin([
        'id' => 'begin-end-pick-list-form',
        'enableClientValidation' => false,
        'validateOnChange' => false,
        'validateOnSubmit' => false,
    ]
); ?>

<?//= $form->field($model, 'barcode_process')->textInput(['class' => 'form-control input-lg'])->label(Yii::t('outbound/forms', 'Barcode process')); ?>
<?//= $form->field($model, 'picking_list_barcode')->textInput(['disabled'=>true])->label(Yii::t('outbound/forms', 'Picking list barcode')); // ,['template'=>'{input}'] ?>
<?= $form->field($model, 'picking_list_barcode')->textInput()->label(Yii::t('outbound/forms', 'Picking list barcode')); // ,['template'=>'{input}'] ?>
<?= $form->field($model, 'employee_barcode')->textInput()->label(Yii::t('outbound/forms', 'Employee barcode')); // , ['template'=>'{input}'] ?>
<?//= $form->field($model, 'picking_list_id', ['template'=>'{input}'])->hiddenInput(); ?>
<?//= $form->field($model, 'employee_id', ['template'=>'{input}'])->hiddenInput(); ?>
<?php ActiveForm::end(); ?>