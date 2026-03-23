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
            'validationUrl'=>Url::to('/warehouseDistribution/colins/outbound/validate-scanning-box'),
//            'enableAjaxValidation'=>true,
        ]
    ); ?>

    <?= $form->field($model, 'box_barcode')->textInput([
        'data' => [
            'url'=>Url::toRoute('/warehouseDistribution/colins/outbound/print-sorting-list-by-box'),
            'validation-url'=>Url::toRoute('/warehouseDistribution/colins/outbound/validate-scanning-box'),
        ]
    ])->label(Yii::t('inbound/forms', 'Box barcode'));?>
    <?= $form->field($model, 'employee_barcode')->textInput()->label(Yii::t('outbound/forms', 'Employee barcode')); ?>

    <?php ActiveForm::end(); ?>
    
</div>
