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

<h1><?= Yii::t('other/api-de-facto/titles', 'Upload inbound DeFacto API') ?></h1>

<div class="get-inbound-order-form">

<?php $form = ActiveForm::begin([
        'id'=>'get-inbound-order-form',
        'enableClientValidation' => false,
        'validateOnChange' => false,
        'validateOnSubmit' => false,
    ]
); ?>

<?= $form->field($model, 'invoice', [
    'labelOptions'=> [
        'label'=>Yii::t('other/api-de-facto/forms', 'Invoice inbound')
    ],
])->textInput(); ?>

<?php ActiveForm::end(); ?>

<div class="form-group">
    <?= \yii\helpers\Html::tag('span', Yii::t('other/api-de-facto/buttons', 'Upload').'<span id="get-inbound-order-button-message"></span>', [
        'class' => 'btn btn-primary',
        'style' => ' margin-left:10px;',
        'id' => 'get-inbound-order-submit-bt',
        'data'=>['url'=>Url::to(['/wms/defacto/api-de-facto/get-inbound-order-form'])]
    ]) ?>
</div>

<div id="inbound-order-uploaded-grid">

</div>