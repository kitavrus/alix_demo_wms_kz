<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 08.11.2019
 * Time: 10:04
 */

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;
use yii\helpers\Url;
use yii\helpers\Html;

\stockDepartment\modules\wms\assets\erenRetail\CheckBoxFormAsset::register($this);
?>

<?//= Html::tag('div',
//    Yii::t('outbound/buttons', 'Print box label'),
//    [
//        'data-url' => Url::toRoute('print-box-label'),
//        'data-validate-url' => Url::toRoute('validate-print-box-label'),
//        'class' => 'btn btn-danger',
//        'id' => 'outboundform-print-box-label-for-order-bt',
//        'style' => 'margin-top:-42px; float:right; font-size: 25px; margin: 0px 5px 0px 5px'
//]) ?>

<div class="scanning-form">
    <?php $form = ActiveForm::begin([
            'id' => 'checkboxform',
            'enableClientValidation' => false,
            'validateOnChange' => false,
            'validateOnSubmit' => false,
        ]
    ); ?>

    <?= $form->field($checkBoxForm, 'employeeBarcode')->textInput([
        'class' => 'form-control input-lg',
        'data-url' => Url::toRoute('employee-barcode')
    ]); ?>

<!--    --><?//= $form->field($checkBoxForm, 'inventoryKey')->textInput([
//        'class' => 'form-control ext-large-input',
//        'data-url' => Url::toRoute('inventory-key')
//    ])
//    ?>

    <?= $form->field($checkBoxForm, 'inventoryId',
        ['template' => "{label}\n{input-group-begin}{counter}{input}{input-group-end}\n{hint}\n{error}\n",
            'parts' => [
                '{input-group-begin}' => '<div class="input-group">',
                '{input-group-end}' => '</div>',
                '{counter}' => '<div class="input-group-addon" style="font-size: 22px;" >' . Yii::t('inbound/titles', 'Products') . ': <strong id="count-products-in-inventory" >0</strong></div>',

            ]
        ]
    )->dropDownList(
        $checkBoxForm->getInventoryKeyList(),
        ['prompt' => '',
            'class' => 'form-control input-lg -ext-large-input',
            'data-url' => Url::to('inventory-id')
        ]
    ); ?>


<!--    --><?//= $form->field($checkBoxForm, 'title')->textInput([
//        'class' => 'form-control -ext-large-input',
//        'data-url' => Url::toRoute('title')
//    ])
//    ?>

    <?= $form->field($checkBoxForm, 'placeAddress')->textInput([
        'class' => 'form-control input-lg -ext-large-input',
        'data-url' => Url::toRoute('place-barcode')
    ])
    ?>

    <?= $form->field($checkBoxForm, 'boxBarcode',
        ['template' => "{label}\n{input-group-begin}{counter}{input}{input-group-end}\n{hint}\n{error}\n",
            'parts' => [
                '{input-group-begin}' => '<div class="input-group">',
                '{input-group-end}' => '</div>',
                '{counter}' => '<div class="input-group-addon" style="font-size: 22px;" >' . Yii::t('inbound/titles', 'Products') . ': <strong id="count-products-in-box" >0</strong></div>',

            ]
        ]
        )->textInput([
        'class' => 'form-control input-lg -ext-large-input',
        'data-url' => Url::toRoute('box-barcode')
    ])
    ?>

    <?= $form->field($checkBoxForm, 'productBarcode')->textInput([
        'class' => 'form-control input-lg -ext-large-input',
        'data-url' => Url::toRoute('product-barcode')
    ])
    ?>

    <?php ActiveForm::end(); ?>

    <div class="row" style="margin: 20px 1px">
        <?= Html::tag('span', Yii::t('outbound/buttons', 'Содержимое короба'), ['data-url' => Url::toRoute('show-products-in-box'), 'class' => 'btn btn-success', 'id' => 'checkboxform-show-products-in-box-bt', 'style' => 'margin-left:10px;']) ?>
        <?= Html::tag('span', Yii::t('outbound/buttons', 'Clear Box'), ['data-url' => Url::toRoute('empty-box'), 'class' => 'btn btn-warning pull-right', 'id' => 'checkboxform-empty-box-bt', 'style' => 'margin-left:10px;']) ?>
    </div>

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
    <div id="show-products-in-box-container" class="table-responsive"></div>
</div>