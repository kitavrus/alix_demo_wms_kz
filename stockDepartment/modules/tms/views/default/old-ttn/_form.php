<?php
use common\modules\transportLogistics\components\TLHelper;
use common\modules\client\models\Client;
use yii\helpers\Html;
use app\modules\transportLogistics\transportLogistics;
use kartik\widgets\ActiveForm;

/* @var $this yii\web\View
/* @var $model common\modules\transportLogistics\models\TlDeliveryProposal */
/* @var $form yii\widgets\ActiveForm */
?>
<?php $form = ActiveForm::begin(); ?>
<?= $form->field($formTTN, 'ttn')->textInput(); ?>
<div class="form-group">
    <?= Html::submitButton('Print', ['class' => 'btn btn-success']) ?>
</div>
<?php ActiveForm::end(); ?>
