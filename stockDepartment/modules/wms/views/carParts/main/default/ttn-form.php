<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 13.11.2017
 * Time: 19:31
 */
use yii\bootstrap\ActiveForm;
$this->title = "Сохраняем ТТНку клиента";
$this->params['breadcrumbs'][] = $this->title;
?>
<h1><?= $this->title ?></h1>
<div class="inbound-order-upload-form">
    <?php $form = ActiveForm::begin([
            'options' => ['enctype' => 'multipart/form-data'],
            'validateOnChange' => false,
        ]
    ); ?>
    <?= $form->field($ttnForm, 'ourTTN')->textInput() ?>
    <?= $form->field($ttnForm, 'clientTTN')->textInput() ?>

    <div class="form-group">
        <?= \yii\bootstrap\Html::submitButton(Yii::t('buttons', 'Сохранить'), ['class' => 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end() ?>
</div>
