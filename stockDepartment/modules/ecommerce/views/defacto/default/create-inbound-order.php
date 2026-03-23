<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 26.08.2019
 * Time: 16:20
 */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$this->title = "Загрузка приходной накладной для Defacto Ecommerce";
$this->params['breadcrumbs'][] = $this->title;
?>
<h1><?= $this->title ?></h1>
<div class="inbound-order-upload-form">
    <?php $form = ActiveForm::begin([
//            'options' => ['enctype' => 'multipart/form-data'],
            'validateOnChange' => false,
        ]
    ); ?>
    <?= $form->field($inboundOrderUploadForm, 'orderNumber')->textInput() ?>
    <?= $form->field($inboundOrderUploadForm, 'qtyBox')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('buttons', 'Upload'), ['class' => 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end() ?>
</div>
