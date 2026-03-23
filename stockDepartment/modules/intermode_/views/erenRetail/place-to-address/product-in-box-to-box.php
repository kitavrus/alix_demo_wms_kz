<?php
/**
 * Created by PhpStorm.
 * User: Igor
 * Date: 08.01.15
 * Time: 7:02
 */

use stockDepartment\modules\wms\managers\erenRetail\placement\ProductInBoxToBoxFormAsset;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $clientsArray common\modules\client\models\Client */
/* @var $boxToBoxForm common\clientObject\main\inbound\forms\BoxToBoxForm */

ProductInBoxToBoxFormAsset::register($this);

?>
<?php $this->title = "Товар из короба в короб"; ?>
<a href="<?= Url::to('/wms/erenRetail/place-to-address/index')?>" class="btn btn-primary">короб на полку</a>
<a href="<?= Url::to('/wms/erenRetail/place-to-address/box-to-box')?>" class="btn btn-primary">из короба в короб</a>
<h1><?= $this->title ?></h1>
<?= Alert::widget([
    'options' => [
        'id' => 'success-list',
        'class' => 'alert-success hidden',
    ],
    'body' => '',
]);
?>
<div class="scan-inbound-form">
    <?php $form = ActiveForm::begin([
            'id' => 'productinboxtoboxform',
            'enableClientValidation' => false,
            'validateOnChange' => false,
            'validateOnSubmit' => false,
        ]
    ); ?>


<!--    --><?//= $form->field($boxToBoxForm, 'fromBox')->textInput([
//        'class' => 'form-control input-lg',
//        'data-url' => Url::to('scan-product-from-box'),
//    ]); ?>

	<?= $form->field($boxToBoxForm, 'fromBox',
		['template' => "{label}\n{input-group-begin}{counter}{input}{input-group-end}\n{hint}\n{error}\n",
			'parts' => [
				'{input-group-begin}' => '<div class="input-group">',
				'{input-group-end}' => '</div>',
				'{counter}' => '<div class="input-group-addon" style="font-size: 22px;" >' . Yii::t('inbound/titles', 'Products') . ': <strong id="count-products-in-from-box" >0</strong></div>',

			]
		]
	)->textInput([
		'class' => 'form-control input-lg -ext-large-input',
		'data-url' => Url::to('scan-product-from-box'),
	])
	?>

<!--	--><?//= $form->field($boxToBoxForm, 'toBox')->textInput([
//		'class' => 'form-control input-lg',
//		'data-url' => Url::to('scan-product-to-box'),
//	]); ?>

	<?= $form->field($boxToBoxForm, 'toBox',
		['template' => "{label}\n{input-group-begin}{counter}{input}{input-group-end}\n{hint}\n{error}\n",
			'parts' => [
				'{input-group-begin}' => '<div class="input-group">',
				'{input-group-end}' => '</div>',
				'{counter}' => '<div class="input-group-addon" style="font-size: 22px;" >' . Yii::t('inbound/titles', 'Products') . ': <strong id="count-products-in-to-box" >0</strong></div>',

			]
		]
	)->textInput([
		'class' => 'form-control input-lg -ext-large-input',
		'data-url' => Url::to('scan-product-to-box'),
	])
	?>

    <?= $form->field($boxToBoxForm, 'productBarcode')->textInput([
        'class' => 'form-control input-lg',
        'data-url' => Url::to('scan-product-product-barcode'),
    ]); ?>



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

	<div id="outbound-items" class="table-responsive">
		<table class="table">
			<tr>
				<th><?= Yii::t('outbound/forms', 'Product Barcode'); ?></th>
				<th><?= Yii::t('outbound/forms', 'Qty'); ?></th>
			</tr>
			<tbody id="outbound-item-body"></tbody>
		</table>
	</div>
</div>