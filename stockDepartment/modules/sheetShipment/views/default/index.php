<?php
/**
 * Created by PhpStorm.
 * User: Igor
 * Date: 15.01.15
 * Time: 18:02
 */

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;


/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $sheetShipmentForm \stockDepartment\modules\sheetShipment\forms\SheetShipmentForm */
?>

<?php $this->title = Yii::t('sheep-shipment/forms', 'PLACE-ADDRESS-TITLE');?>
<?php \stockDepartment\modules\sheetShipment\assets\SheepShipmentAssets::register($this); ?>

<h1>Размещаем заказы в адреса отгрузки</h1>
<div id="messages-container">
    <div id="messages-base-line"></div>
    <?= Alert::widget([
        'options' => [
            'id' => 'messages-list',
            'class' => 'alert-info hidden',
        ],
        'body' => '<span id="messages-list-body"></span>',
    ]);
    ?>
</div>

<?php $form = ActiveForm::begin([
        'id'=>'sheet-shipment-form',
        'enableClientValidation'=>false,
        'validateOnChange'=>false,
        'validateOnSubmit'=>false,
    ]
); ?>

<?= $form->field($sheetShipmentForm, 'boxBarcode',['labelOptions'=>['id'=>'box-barcode-label']])->textInput(); ?>
<?= $form->field($sheetShipmentForm, 'placeAddress',['labelOptions'=>['id'=>'place-address-label']])->textInput(); ?>
<?php ActiveForm::end(); ?>

<div id="error-container">
    <div id="error-base-line"></div>
    <?= Alert::widget([
        'options' => [
            'id' => 'error-list',
            'class' => 'alert-danger hidden',
        ],
        'body' => '',
    ]);
    ?>
</div>