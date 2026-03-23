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
use app\modules\returnOrder\assets\ReturnAsset;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model app\modules\returnOrder\models\ReturnForm */

ReturnAsset::register($this);
?>

<?php $this->title = Yii::t('return/titles', 'Return Orders'); ?>
<div id="messages-container">
    <div id="messages-base-line"></div>
    <?= Alert::widget([
        'options' => [
            'id' => 'messages-list',
            'class' => 'alert-info hidden',
        ],
        'body' => '<span id="messages-list-body"></span>',
    ]);
    ?>
</div>
<div class="return-order-process-form">
    <?php $form = ActiveForm::begin([
            'id' => 'return-process-form',
            'enableClientValidation' => false,
            'validateOnChange' => false,
            'validateOnSubmit' => false,
        ]
    ); ?>

    <?= $form->field($model, 'box_barcode')->textInput([
        'class' => 'form-control input-lg ext-large-input',
    ]); ?>

    <?= $form->field($model,'order_number'
        , [
        'template' => "{label}\n{input-group-begin}{counter}{input}{input-group-end}\n{hint}\n{error}\n", //{button-right}
        'parts' => [
            '{label}' => '<label for="returnform-box_barcode">' . Yii::t('return/forms', 'Order number') . '</label>',
            '{input-group-begin}' => '<div class="input-group">',
            '{input-group-end}' => '</div>',
            '{counter}' => '<div class="input-group-addon" style="font-size:30px;" id="message-return-order"></div>',
//            '{button-right}' => '<div class="input-group-addon" style="background-color: none; border: none; border-radius: none;" ><span class="btn btn-success btn-xs" data-url-value="' . Url::toRoute(['clear-box']) . '" id="clear-box-bt">' . Yii::t('return/buttons', 'Clear Box') . '</span></div>'
        ]
    ]
    )->textInput(
        [
            'class' => 'form-control input-lg ext-large-input',
        ]
    ); ?>

    <?= $form->field($model, 'new_return_order_id', ['template'=>'{input}'])->hiddenInput(); ?>

    <?php ActiveForm::end(); ?>

    <div class="row" style="margin: 20px 1px">
        <?= Html::tag('span', Yii::t('return/buttons', 'Print box label'), ['data-url' => Url::toRoute('print-box-barcode'), 'class' => 'btn btn-success', 'id' => 'return-form-print-box-label-bt', 'style' => 'margin-right:10px; float:right;']) ?>
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
</div>