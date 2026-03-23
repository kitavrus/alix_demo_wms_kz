<?php
/**
 * Created by PhpStorm.
 * User: Kitavrus
 * Date: 17.04.15
 * Time: 10:45
 */
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;
use yii\helpers\Url;
use stockDepartment\modules\crossDock\assets\CrossDockAsset;
use yii\bootstrap\Modal;
?>

<?php $this->title = Yii::t('inbound/titles', 'Colins allocate list'); ?>
<div class="get-allocate-list-form">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php $form = ActiveForm::begin([
            'id' => 'allocate-list-form',
            'enableClientValidation' => false,
            'validateOnChange' => false,
            'validateOnSubmit' => false,
            'validationUrl'=>Url::to('/wms/colins/outbound/validate-scanning-box'),
//            'enableAjaxValidation'=>true,
            'options' => [
                'data-printType' => \Yii::$app->params['printType']
            ]
        ]
    ); ?>

    <?= $form->field($model, 'box_barcode')->textInput([
        'data' => [
            'url'=>Url::toRoute('/wms/colins/outbound/print-sorting-list-by-box'),
            'validation-url'=>Url::toRoute('/wms/colins/outbound/validate-scanning-box'),
        ]
    ])->label(Yii::t('inbound/forms', 'Box barcode'));?>
    <?= $form->field($model, 'employee_barcode')->textInput()->label(Yii::t('outbound/forms', 'Employee barcode')); ?>

    <?php ActiveForm::end(); ?>
</div>
<iframe style="display: none" name="frame-print-alloc-list" src="#" width="468" height="468">
