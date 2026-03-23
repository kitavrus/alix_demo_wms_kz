<?php
/**
 * Created by PhpStorm.
 * User: Igor
 * Date: 15.01.15
 * Time: 18:02
 */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;
use yii\helpers\Url;

\app\modules\ecommerce\assets\defacto\InventoryAsset::register($this);
/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
?>

<?php $this->title = Yii::t('stock/titles', 'Inventory');?>

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

<div class="stock-accommodation-form">
    <?php $form = ActiveForm::begin([
            'id'=>'stock-inventory-form',
            'enableClientValidation'=>false,
            'validateOnChange'=>false,
            'validateOnSubmit'=>false,
        ]
    ); ?>

    <?= $form->field($InventoryForm, 'inventory_id')->dropDownList(\common\ecommerce\entities\EcommerceInventory::getActiveInventory()) ?>

    <?= $form->field($InventoryForm, 'place_address_barcode'
//		,[
//	'template' => "{label}\n{input-group-begin}{input}{button-right}{input-group-end}\n{hint}\n{error}\n",
//		'parts' => [
//			'{label}' => '<label for="inboundform-box_barcode">' . Yii::t('inbound/forms', 'Box Barcode') . '</label>',
//		'{input-group-begin}' => '<div class="input-group">',
//		'{input-group-end}' => '</div>',
//		'{counter}' => '<div class="input-group-addon" >' . Yii::t('inbound/titles', 'Products') . ': <strong id="inventoryform-count-product-in-box" >0</strong>&nbsp;&nbsp;&nbsp;&nbsp;<span class="in-box">' . Yii::t('inbound/titles', 'In box') . ': </span></div>',
//		'{button-right}' => '<div class="input-group-addon" style="background-color: none; border: none; border-radius: none;" ><span class="btn btn-success btn-xs" data-url="' . Url::toRoute(['Start']) . '" id="inventoryform-start-bt">' . Yii::t('inbound/buttons', 'Start inventory') . '</span></div>'
//	]
//	]
    )->textInput(['data'=>['url'=>Url::toRoute('secondary-address')]]); ?>

    <!--	--><?//= $form->field($InventoryForm, 'primary_address',['labelOptions'=>['id'=>'to-label']])->textInput(['data'=>['url'=>Url::toRoute('primary-address')]]); ?>
    <?= $form->field($InventoryForm, 'box_address_barcode', [
        'template' => "{label}\n{input-group-begin}{counter}{input}{button-right}{input-group-end}\n{hint}\n{error}\n",
        'parts' => [
//			'{label}' => '<label for="inboundform-box_barcode">' . Yii::t('inbound/forms', 'Box Barcode') . '</label>',
            '{input-group-begin}' => '<div class="input-group">',
            '{input-group-end}' => '</div>',
            '{counter}' => '<div class="input-group-addon" >' . Yii::t('inbound/titles', 'Products') . ': <strong id="inventoryform-count-product-in-box" >0</strong>&nbsp;&nbsp;&nbsp;&nbsp;<span class="in-box">' . Yii::t('inbound/titles', 'In box') . ': </span></div>',
            '{button-right}' => '<div class="input-group-addon" style="background-color: none; border: none; border-radius: none;" ><span class="btn btn-success btn-xs" data-url="' . Url::toRoute(['clear-box']) . '" id="inventoryform-clear-box-bt">' . Yii::t('inbound/buttons', 'Clear Box') . '</span></div>'
        ]
    ])->textInput(
        [
//			'id' => 'inbound-form-box_barcode',
            'class' => 'form-control input-lg hidden',
//			'data-url' => Url::to('/warehouseDistribution/defacto/inbound/validate-scanned-box')
            'data'=>['url'=>Url::toRoute('primary-address')]
        ]
    ); ?>



    <?= $form->field($InventoryForm, 'product_barcode',['labelOptions'=>['id'=>'to-label']])->textInput([
        'data'=>['url'=>Url::toRoute('product-barcode')],
        'class' => 'form-control input-lg hidden',
    ]); ?>

    <?php ActiveForm::end(); ?>
    <?= Html::tag('span', Yii::t('buttons', 'Print inventory diff items'), ['class' => 'btn btn-warning ', 'style' => '', 'id' => 'print-inventory-diff-list-bt','data-url'=>Url::to(['print-diff-list'])]) ?>
    <?= Html::tag('span', Yii::t('buttons', 'Print inventory accepted items'), ['class' => 'btn btn-info ', 'style' => '', 'id' => 'print-inventory-accepted-item-bt','data-url'=>Url::to(['print-accepted-list'])]) ?>


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
</div>