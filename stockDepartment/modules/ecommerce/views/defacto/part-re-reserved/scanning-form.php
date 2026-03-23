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
use yii\helpers\Url;
use yii\helpers\Html;

\app\modules\ecommerce\assets\defacto\PartReReservedFormAsset::register($this);
?>
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
<h1>
    <div style = " float:left; font-size: 25px;">Найти товар для перерезерва Ecommerce </div>
</h1>
<br />
<br />
<div class="scanning-form">
    <?php $form = ActiveForm::begin([
            'id' => 'partrereservedform',
            'enableClientValidation' => false,
            'validateOnChange' => false,
            'validateOnSubmit' => false,
        ]
    ); ?>

    <?= $form->field($model, 'employeeBarcode')->textInput([
        'class' => 'form-control ext-large-input',
        'data-url' => Url::toRoute('employee-barcode')
    ])
    ?>

    <?= $form->field($model, 'pickListBarcode'
        ,
        ['template' => "{label}\n{input-group-begin}{button-right}{input}{input-group-end}\n{hint}\n{error}\n",
            'parts' => [
                '{input-group-begin}' => '<div class="input-group">',
                '{input-group-end}' => '</div>',
                '{button-right}' => '<div class="input-group-addon" style="font-size: 62px;" id="pick-list-barcode-qty">0</div>'
            ]
        ]
    )->textInput([
        'class' => 'form-control ext-large-input',
        'data-url' => Url::toRoute('pick-list-barcode')
    ])
    ?>

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
    <div id="show-items" class="table-responsive"></div>
    <div id="show-other-product-address" class="table-responsive"></div>
</div>