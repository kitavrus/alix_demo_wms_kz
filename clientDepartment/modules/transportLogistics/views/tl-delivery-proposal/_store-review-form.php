<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;

//use yii\widgets\ActiveForm;
//use dosamigos\datepicker\DatePicker;
use yii\bootstrap\Modal;
use yii\helpers\Url;
//use frontend\modules\client\models\Client;
use common\modules\transportLogistics\components\TLHelper;
use app\modules\transportLogistics\transportLogistics;
use kartik\datecontrol\DateControl;
use common\modules\client\models\Client;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use kartik\widgets\StarRating;

//use frontend\modules\transportLogistics\models\TlAgents;
//use frontend\modules\transportLogistics\models\TlCars;

/* @var $this yii\web\View */
/* @var $model common\modules\transportLogistics\models\TlDeliveryProposal */
/* @var $form yii\widgets\ActiveForm */
?>


<?php $form = ActiveForm::begin([
    'action' => ['/store/store-review/view', 'id' => $model->id],
]); ?>
<?php
//if(!$isAlmaty) {
//    echo $form->field($model, 'delivery_datetime')->widget(DateControl::className(), ['type'=>DateControl::FORMAT_DATETIME]);
//}
//?>

<?= $form->field($model, 'number_of_places')->textInput() ?>
<?= $form->field($model, 'rate')->widget(StarRating::className(), [
    'pluginOptions' => ['step' => 1],
]); ?>

<?= $form->field($model, 'comment')->textarea(['rows' => 6]) ?>
<?php //if($isAlmaty) { ?>
<?= $form->field($model, 'delivery_code')->passwordInput() ?>
<?php //} ?>

<div class="form-group">
    <?= Html::submitButton($model->isNewRecord ? Yii::t('transportLogistics/buttons', 'Create') : Yii::t('transportLogistics/buttons', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
</div>

<?php ActiveForm::end(); ?>