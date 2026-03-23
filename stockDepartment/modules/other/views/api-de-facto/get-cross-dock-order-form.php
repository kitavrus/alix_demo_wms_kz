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

<h1><?= Yii::t('other/api-de-facto/titles', 'Upload cross-dock DeFacto API') ?></h1>

<div class="get-cross-dock-order-form">

<?php $form = ActiveForm::begin([
        'id'=>'get-cross-dock-order-form',
        'enableClientValidation' => false,
        'validateOnChange' => false,
        'validateOnSubmit' => false,
    ]
); ?>

<?= $form->field($model, 'invoice', [
    'labelOptions'=> [
        'label'=>Yii::t('other/api-de-facto/forms', 'Invoice cross-dock')
    ],
])->textInput(); ?>

<?php ActiveForm::end(); ?>

<div class="form-group">
    <?= \yii\helpers\Html::tag('span', Yii::t('other/api-de-facto/buttons', 'Upload').'<span id="get-cross-dock-order-button-message"></span>', [
        'class' => 'btn btn-primary',
        'style' => ' margin-left:10px;',
        'id' => 'get-cross-dock-order-submit-bt',
        'data'=>['url'=>Url::to(['/other/api-de-facto/get-cross-dock-order-form'])]
    ]) ?>
</div>

    <div id="cross-dock-order-uploaded-grid">

    </div>