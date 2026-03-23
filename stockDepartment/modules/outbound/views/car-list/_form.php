<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\modules\client\models\Client;
use kartik\select2\Select2;
use common\modules\transportLogistics\components\TLHelper;

/* @var $this yii\web\View */
/* @var $model common\modules\transportLogistics\models\TlDeliveryProposalDefaultSubRoute */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tl-delivery-proposal-default-sub-route-form">

    <?php $form = ActiveForm::begin(); ?>
    <?php if($model->isNewRecord){ ?>
    <?= $form->field($model, 'client_id')->dropDownList($clientsArray, ['prompt' =>Yii::t('transportLogistics/titles', 'Select client')]); ?>

    <?=
    $form->field($model, 'from_point_id')->widget(Select2::classname(), [
        'language' => 'ru',
        'data' => $storeArray,
        'options' => ['placeholder' => Yii::t('transportLogistics/titles', 'Select route from')],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]);
    ?>

    <?=
    $form->field($model, 'to_point_id')->widget(Select2::classname(), [
        'language' => 'ru',
        'data' => $storeArray,
        'options' => ['placeholder' => Yii::t('transportLogistics/titles', 'Select route from')],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]);
    ?>
    <?php } ?>
    <?= $form->field($model, 'title')->textInput(); ?>
    <?= $form->field($model, 'description')->textarea(['rows' => '8']); ?>
    <?= $form->field($model, 'mc')->textInput(); ?>
    <?= $form->field($model, 'kg')->textInput(); ?>
    <?= $form->field($model, 'accepted_number_places_qty')->textInput(); ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('buttons', 'Create') : Yii::t('buttons', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
