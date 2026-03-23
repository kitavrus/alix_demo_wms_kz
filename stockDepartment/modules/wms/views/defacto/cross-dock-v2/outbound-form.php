<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 06.10.2015
 * Time: 11:04
 */
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;
use yii\helpers\Url;
use yii\bootstrap\Modal;

?>
<?php $this->title = Yii::t('cross-dock/titles', 'Scanning by store'); ?>

<div class="cross-dock-process-form">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin([
            'id' => 'outbound-cross-dock-form',
            'enableClientValidation' => false,
            'validateOnChange' => false,
            'validateOnSubmit' => false,
        ]
    ); ?>

    <?= $form->field($formModel, 'internal_barcode')->textInput(['data-url' => Url::toRoute('internal-barcode-outbound-form'), 'class' => 'form-control input-lg']); ?>

    <?= $form->field($formModel, 'to_point',
        ['template' => "{label}\n{input-group-begin}{counter}{input}{store}{input-group-end}\n{hint}\n{error}\n",
            'parts' => [
                '{input-group-begin}' => '<div class="input-group">',
                '{input-group-end}' => '</div>',
                '{counter}' => '<div class="input-group-addon" >' . Yii::t('cross-dock/titles', 'Boxes') . ': <strong id="count-box-in-party-outbound-cross-dock-form" >0</strong>&nbsp;&nbsp;&nbsp;&nbsp;<span class="in-order-party">' . Yii::t('cross-dock/titles', 'In store') . ': </span></div>',
                '{store}' => '<div class="input-group-addon" id="store-name-outbound-cross-dock-form"></div>',
            ]
        ]
    )->textInput(['data-url' => Url::toRoute('to-store-outbound-form'), 'class' => 'form-control input-lg',]); ?>


    <?= $form->field($formModel, 'box_barcode')->textInput(['data-url' => Url::toRoute('scanning-outbound-form'), 'class' => 'form-control input-lg',]); ?>

    <?= $form->field($formModel, 'box_size_code')->textInput(['data-url' => Url::toRoute('scanning-box-size-code'), 'class' => 'form-control input-lg',]); ?>


    <!--    --><?php //= $form->field($formModel, 'validate_type')->hiddenInput(['id'=>'outbound-cross-dock-form-validate-type'])->label(false); ?>
    <!--    --><?php //= $form->field($formModel, 'client_id')->hiddenInput(['id'=>'outbound-cross-dock-form-client_id'])->label(false); ?>

    <?php ActiveForm::end(); ?>
</div>
<!--// FOR MOBILE KEYBOARD BEGIN-->
<div class="container" id="container-button" style="margin: 10px;">
    <div class="row">
        <button type="button" class="btn btn-default key1">-</button>
        <button type="button" class="btn btn-default key1">0</button>
        <button type="button" class="btn btn-default key1">1</button>
        <button type="button" class="btn btn-default key1">2</button>
        <button type="button" class="btn btn-default key1">3</button>
        <button type="button" class="btn btn-default key1">4</button>
        <button type="button" class="btn btn-default key1">5</button>
        <button type="button" class="btn btn-default key1">6</button>
        <button type="button" class="btn btn-default key1">7</button>
        <button type="button" class="btn btn-default key1">8</button>
        <button type="button" class="btn btn-default key1">9</button>
        <button type="button" class="btn btn-danger  key1">enter</button>
        <button type="button" class="btn btn-warning key1">del</button>
    </div>
</div>
<!--// FOR MOBILE KEYBOARD END-->
<?= Html::tag('span', Yii::t('cross-dock/buttons','List differences'), [
    'class' => 'btn btn-danger btn-lg',
    'id' => 'outbound-cross-dock-list-differences-bt',
//    'data' => ['url' => Url::toRoute('print-list-differences')],
    'data' => ['url' => Url::toRoute('get-id')],
]) ?>

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