<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 03.04.15
 * Time: 16:31
 */


use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

?>

<h1><?= Yii::t('other/api-de-facto/titles', 'Upload outbound DeFacto API') ?></h1>

<div class="get-outbound-order-form">

    <?php $form = ActiveForm::begin([
            'id' => 'get-outbound-order-form',
            'enableClientValidation' => false,
            'validateOnChange' => false,
            'validateOnSubmit' => false,
        ]
    ); ?>

    <?= $form->field($model, 'invoice', [
        'labelOptions' => [
            'label' => Yii::t('other/api-de-facto/forms', 'Invoice outbound')
        ],
    ])->textInput(); ?>

    <?php ActiveForm::end(); ?>

    <div class="form-group">
        <?= \yii\helpers\Html::tag('span', Yii::t('other/api-de-facto/buttons', 'Upload') . '<span id="get-outbound-order-button-message"></span>', [
            'class' => 'btn btn-primary',
            'style' => ' margin-left:10px;',
            'id' => 'get-outbound-order-submit-bt',
            'data' => ['url' => Url::to(['/wms/defacto/api-de-facto/get-outbound-order-form'])]
        ]) ?>
    </div>
    <div id="error-container">
        <div id="error-base-line"></div>
        <?= \yii\bootstrap\Alert::widget([
            'options' => [
                'id' => 'error-list',
                'class' => 'alert-danger hidden',
            ],
            'body' => '',
        ]);
        ?>
    </div>
    <div id="outbound-order-uploaded-grid">

    </div>