<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 03.04.2017
 * Time: 19:45
 */
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;

\app\modules\returnOrder\assets\ReturnTmpOrderAsset::register($this);

?>

<?php $this->title = Yii::t('return/titles', 'Return TMP Orders'); ?>
<div class="order-tmp-form">
    <?php $form = ActiveForm::begin([
            'id' => 'return-tmp-orders-form',
            'enableClientValidation' => false,
            'validateOnChange' => false,
            'validateOnSubmit' => false,
        ]
    ); ?>

    <?= $form->field($formModel, 'ttn', [
        'template' => "{label}\n{input-group-begin}{input}{button-right}{input-group-end}\n{hint}\n{error}\n",
        'parts' => [
            '{input-group-begin}' => '<div class="input-group">',
            '{input-group-end}' => '</div>',
            '{counter}' => '<div class="input-group-addon" ></div>',
            '{button-right}' => '<div class="input-group-addon" style="background-color: none; border: none; border-radius: none;"><span id="qty-places-in-ttn"></span>&nbsp;&nbsp;/&nbsp;&nbsp;<span id="qty-scanned-in-ttn"></span></div>'
        ]
    ])->textInput(
        [
            'class' => 'form-control input-lg selected-on-click',
            'data-url' => Url::toRoute('check-ttn')
        ]
    ); ?>

    <?= $form->field($formModel, 'our_box_to_stock_barcode', [
        'template' => "{label}\n{input-group-begin}{input}{button-right}{input-group-end}\n{hint}\n{error}\n",
        'parts' => [
            '{input-group-begin}' => '<div class="input-group">',
            '{input-group-end}' => '</div>',
            '{counter}' => '<div class="input-group-addon" >' .''. '</div>',
            '{button-right}' => '<div class="input-group-addon" style="background-color: none; border: none; border-radius: none;" >'.''.'</div>'
        ]
    ])->textInput(
        [
            'class' => 'form-control input-lg selected-on-click',
            'data-url' => Url::toRoute('check-our-box-stock-barcode')
        ]
    ); ?>

    <?= $form->field($formModel, 'client_box_barcode', [
        'template' => "{label}\n{input-group-begin}{input}{button-right}{input-group-end}\n{hint}\n{error}\n",
        'parts' => [
            '{input-group-begin}' => '<div class="input-group">',
            '{input-group-end}' => '</div>',
            '{button-right}' => '<div class="input-group-addon" style="background-color: none; border: none; border-radius: none;" >'.''.'</div>'
        ]
    ])->textInput(
        [
            'class' => 'form-control input-lg selected-on-click',
            'data-url' => Url::toRoute('check-client-box-barcode')
        ]
    ); ?>

    <?php ActiveForm::end(); ?>

    <div class="form-group">
<!--        --><?php //echo  \yii\bootstrap\Html::tag('span',  'Закрыть ТТНку'.'<span id="return-messages-process"> </span>', ['class' => 'btn btn-danger pull-right', 'data-url' => Url::toRoute('confirm-order'), 'style' => ' margin-left:10px;', 'id' => 'return-tmp-order-form-accept-bt']) ?>
        <?= Html::tag('span',  'Не размещенные', ['data-url' => Url::toRoute('print-without-secondary-address'), 'class' => 'btn btn-warning', 'id' => 'return-print-without-secondary-address-bt', 'style' => 'margin-right:10px;']) ?>
        <?= Html::tag('span',  'Размещенные короба', ['data-url' => Url::toRoute('print-with-secondary-address'), 'class' => 'btn btn-primary', 'id' => 'return-print-with-secondary-address-bt', 'style' => 'margin-right:10px;']) ?>
    </div>
    <div id="countdown" data-on="0"></div>
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