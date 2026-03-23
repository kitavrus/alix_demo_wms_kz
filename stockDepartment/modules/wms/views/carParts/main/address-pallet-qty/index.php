<?php
/**
 * Created by PhpStorm.
 * User: Igor
 * Date: 08.01.15
 * Time: 7:02
 */

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */

\stockDepartment\modules\wms\assets\carParts\main\AddressPalletQtyFormAsset::register($this);

?>
<?php $this->title = "Размещение товара в адрес на полке"; ?>
<h1><?= $this->title ?></h1>

<div class="scan-inbound-form">
    <?php $form = ActiveForm::begin([
            'id' => 'addresspalletqtyform',
            'enableClientValidation' => false,
            'validateOnChange' => false,
            'validateOnSubmit' => false,
        ]
    ); ?>

    <?= $form->field($placeToAddressForm, 'placeAddress',
        ['template' => "{label}\n{input-group-begin}{counter}{input}{input-group-end}\n{hint}\n{error}\n",
            'parts' => [
                '{input-group-begin}' => '<div class="input-group">',
                '{input-group-end}' => '</div>',
                '{counter}' => '<div class="input-group-addon" >Коробов в адресе: <strong id="count-box-in-address">0</strong></div>',

            ]
        ])->textInput([
        'class' => 'form-control input-lg',
        'data-url' => Url::to('scan-place-address'),
    ]); ?>

    <?= $form->field($placeToAddressForm, 'palletPlaceQty')->textInput([
        'class' => 'form-control input-lg col-xs-2',
        'data-url' => Url::to('add-pallet-place-qty'),
    ]); ?>

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
</div>