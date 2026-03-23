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
        ]
    ); ?>

    <?= $form->field($model, 'box_barcode')->textInput()->label(Yii::t('inbound/forms', 'Box barcode'));?>


    <?php ActiveForm::end(); ?>

<?//= $showButton ? Html::tag('span', Yii::t('inbound/buttons', 'Print'), ['class' => 'btn btn-danger pull-right', 'style' => ' margin-left:10px;', 'data-url' => '', 'id' => 'print-allocate-list-bt']) : ''?>
</div>

