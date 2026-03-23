<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $bindForm stockDepartment\modules\intermode\controllers\bind\domain\BindQrCodeForm */

$this->title = Yii::t('wms/titles', 'Связать DM и наш ШК');
?>
<h1><?= $this->title ?></h1>

<div id="messages-container">
    <div id="messages-base-line"></div>
</div>

<div class="bind-process-form">
    <?php $form = ActiveForm::begin(
        [
            'id' => 'bind-qr-code-process-form',
            'enableClientValidation' => false,
            'validateOnChange' => false,
            'validateOnSubmit' => false,
            'options' => [
                'data-printType' => \Yii::$app->params['printType']
            ]
        ]
    );
    ?>

    <?= $form->field(
        $bindForm,
        'box_barcode',
        [
        'template' => "{label}\n{input-group-begin}{counter}{input}{input-group-end}\n{hint}\n{error}\n",
        'parts' => [
            '{label}' => '<label for="bindqrcodeform-box_barcode">' . Yii::t('inbound/forms', 'Box Barcode') . '</label>',
            '{input-group-begin}' => '<div class="input-group">',
            '{input-group-end}' => '</div>',
            '{counter}' => '<div class="input-group-addon" >' . Yii::t('inbound/titles', 'Products') . ': <strong id="count-product-in-box" >0</strong>&nbsp;&nbsp;&nbsp;&nbsp;<span class="in-box">' . Yii::t('inbound/titles', 'In box') . ': </span></div>'
        ]
    ])->textInput(
            [
                'id' => 'bindqrcodeform-box_barcode',
                'class' => 'form-control input-lg',
                'data-url' => Url::to(['/intermode/bind/bind-qr-code/validate-scanned-box'])
            ]
        );
    ?>

    <?= $form->field(
        $bindForm,
        'product_barcode',
        [
            'labelOptions' => ['label' => Yii::t('inbound/forms', 'Product Barcode')],
            'template' => "{label}\n{input}\n{hint}\n{error}\n",
            'parts' => [
                '{label}' => '<label for="bindqrcodeform-product_barcode">' . Yii::t('inbound/forms', 'Product Barcode') . '</label>'
            ]
        ]
    )->textInput(
            [
                'id' => 'bindqrcodeform-product_barcode',
                'class' => 'form-control input-lg',
                'data-url' => Url::to('/intermode/bind/bind-qr-code/scan-product-in-box')
            ]
        );
    ?>


    <?= $form->field(
        $bindForm,
        'our_product_barcode',
        [
            'labelOptions' => ['label' => Yii::t('inbound/forms', 'Наш ШК товара')],
            'template' => "{label}\n{input}\n{hint}\n{error}\n",
            'parts' => [
                '{label}' => '<label for="bindqrcodeform-our_product_barcode">' . Yii::t('inbound/forms', 'Наш ШК товара') . '</label>'
            ]
        ]
    )->textInput(
            [
                'id' => 'bindqrcodeform-our_product_barcode',
                'class' => 'form-control input-lg',
                'data-url' => Url::to('/intermode/bind/bind-qr-code/scan-our-product'),
            ]
        );
    ?>

    <?= $form->field(
        $bindForm,
        'bind_qr_code',
        [
            'labelOptions' => ['label' => Yii::t('inbound/forms', 'Наш короб')],
            'template' => "{label}\n{input}\n{hint}\n{error}\n",
            'parts' => [
                '{label}' => '<label for="bindqrcodeform-bind_qr_code">' . Yii::t('inbound/forms', 'QR code') . '</label>'
            ]
        ]
    )->textInput(
            [
                'id' => 'bindqrcodeform-bind_qr_code',
                'class' => 'form-control input-lg',
                'data-url' => Url::to('/intermode/bind/bind-qr-code/bind-qr-code'),
            ]
        );
    ?>

    <?php ActiveForm::end(); ?>

    <?php
    $this->registerJsFile('@web/js/intermode/bind-qr-code.js', [
        'depends' => [\yii\web\JqueryAsset::class],
    ]);
    ?>