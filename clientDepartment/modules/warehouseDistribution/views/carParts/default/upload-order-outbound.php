<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 29.09.2017
 * Time: 13:39
 */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$this->title = "Загрузка сборки накладной";
$this->params['breadcrumbs'][] = $this->title;
?>
<h1><?= $this->title ?></h1>
<div class="inbound-order-upload-form">
    <?php $form = ActiveForm::begin([
            'options' => ['enctype' => 'multipart/form-data'],
            'validateOnChange' => false,
        ]
    ); ?>
    <?= $form->field($outboundOrderUploadForm, 'storeId')->dropDownList($stores) ?>
    <?= $form->field($outboundOrderUploadForm, 'orderNumber',
        [
            'errorOptions' => [
                'encode' => false,
                'class' => 'help-block'
            ]
        ]
    )->textInput() ?>
    <?= $form->field($outboundOrderUploadForm, 'comment',
        [
            'errorOptions' => [
                'encode' => false,
                'class' => 'help-block'
            ]
        ]
    )->textInput() ?>
    <?= $form->field($outboundOrderUploadForm, 'originalOrderFile')->fileInput() ?>
    <?= $form->field($outboundOrderUploadForm, 'preparedOrderFile')->fileInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('buttons', 'Upload'), ['class' => 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end() ?>
</div>
