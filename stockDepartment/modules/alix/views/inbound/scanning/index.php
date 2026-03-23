<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $orderNumberArray common\modules\inbound\models\InboundOrder */
/* @var $clientsArray common\modules\client\models\Client */
/* @var $inboundForm stockDepartment\modules\inbound\models\InboundForm */

stockDepartment\modules\intermode\controllers\inbound\domain\assets\InboundAsset::register($this);
?>

<?php $this->title = Yii::t('inbound/titles', 'Inbound Orders'); ?>
<h1><?= $this->title ?></h1>

<div class="order-process-form">
    <?php $form = ActiveForm::begin([
            'id' => 'inbound-process-form',
            'enableClientValidation' => false,
            'validateOnChange' => false,
            'validateOnSubmit' => false,
            'options' => [
                'data-printType' => \Yii::$app->params['printType']
            ]
        ]
    ); ?>

    <?php echo $form->field($inboundForm, 'client_id')->hiddenInput()->label(false)?>

	<div class="btn-group" data-toggle="buttons">
		<?= $form->field($inboundForm, 'addExtraProduct')->radioList(
			[
				'0'=>'НЕ ПРИНИМАЕМ ПЛЮСЫ',
				'1'=>'ВНИМАНИЕ!!! принимаем плюсы',
			],[
			'item' => function($index,$label,$name,$checked,$value){
				return Html::radio($name,
					$checked,
					[
						'label' => $label,
						'value' => $value,
						'labelOptions' => ['class' => 'btn '.($index ? 'btn-danger' : 'btn-success').' active']
					]);

			}]); ?>
	</div>
	<br />
	<div class="btn-group" data-toggle="buttons">
		<?= $form->field($inboundForm, 'withDatamatrix')->radioList(
			[
				'0'=>'Без Дата-матрицы',
				'1'=>'С Дата-матрицы',
			],[
			'item' => function($index,$label,$name,$checked,$value){
				return Html::radio($name,
					$checked,
					[
						'label' => $label,
						'value' => $value,
						'labelOptions' => ['class' => 'btn '.($index ? 'btn-danger' : 'btn-success').' active',"id"=>"scaninboundform-withdatamatrix"]
					]);

			}]); ?>
	</div>

    <?= $form->field($inboundForm, 'party_number',
        ['template' => "{label}\n{input-group-begin}{counter}{input}{input-group-end}\n{hint}\n{error}\n",
            'parts' => [
//                '{label}' => '<label for="inbound-form-party-number">' . Yii::t('inbound/forms', 'Order') . '</label>',
                '{input-group-begin}' => '<div class="input-group">',
                '{input-group-end}' => '</div>',
                '{counter}' => '<div class="input-group-addon" >' . Yii::t('inbound/titles', 'Products') . ': <strong id="count-products-in-party" >0</strong>&nbsp;&nbsp;&nbsp;&nbsp;<span class="in-order-party">' . Yii::t('inbound/titles', 'In party') . ': </span></div>',

            ]
        ]
    )->dropDownList(
        $partyNumberArray,
        ['prompt' => '',
            'id' => 'inbound-form-party-number',
            'class' => 'form-control input-lg',
//            'data-url' => '/wms/erenRetail/inbound/get-in-process-inbound-orders-by-client-id'
            //'data-url' => '/intermode/inbound/scanning/get-in-process-inbound-orders-by-client-id'
            'data-url' => '/intermode/inbound/scanning/get-in-process-inbound-orders-by-party-id'
        ]
    ); ?>

    <?= $form->field($inboundForm, 'order_number',
        ['template' => "{label}\n{input-group-begin}{counter}{input}{input-group-end}\n{hint}\n{error}\n",
            'parts' => [
                '{label}' => '<label for="inbound-form-order-number">' . Yii::t('inbound/forms', 'Order') . '</label>',
                '{input-group-begin}' => '<div class="input-group">',
                '{input-group-end}' => '</div>',
                '{counter}' => '<div class="input-group-addon" >' . Yii::t('inbound/titles', 'Products') . ': <strong id="count-products-in-order" >0</strong>&nbsp;&nbsp;&nbsp;&nbsp;<span class="in-order">' . Yii::t('inbound/titles', 'In order') . ': </span></div>',

            ]
        ]
    )->dropDownList(
        [],
        ['prompt' => '',
            'id' => 'inbound-form-order-number',
            'class' => 'form-control input-lg',
//            'data-url' => Url::to('/wms/erenRetail/inbound/get-scanned-product-by-id')
            'data-url' => Url::to('/intermode/inbound/scanning/get-scanned-product-by-id')
        ]
    ); ?>

    <?= $form->field($inboundForm, 'box_barcode', [
        'template' => "{label}\n{input-group-begin}{counter}{input}{button-right}{input-group-end}\n{hint}\n{error}\n",
        'parts' => [
            '{label}' => '<label for="inboundform-box_barcode">' . Yii::t('inbound/forms', 'Box Barcode') . '</label>',
            '{input-group-begin}' => '<div class="input-group">',
            '{input-group-end}' => '</div>',
            '{counter}' => '<div class="input-group-addon" >' . Yii::t('inbound/titles', 'Products') . ': <strong id="count-product-in-box" >0</strong>&nbsp;&nbsp;&nbsp;&nbsp;<span class="in-box">' . Yii::t('inbound/titles', 'In box') . ': </span></div>',
            '{button-right}' => '<div class="input-group-addon" style="background-color: none; border: none; border-radius: none;" ><span class="btn btn-success btn-xs" data-url-value="' . Url::toRoute(['clear-box']) . '" id="clear-box-bt">' . Yii::t('inbound/buttons', 'Clear Box') . '</span></div>'
        ]
    ])->textInput(
        [
            'id' => 'inbound-form-box_barcode',
            'class' => 'form-control input-lg',
//            'data-url' => Url::to('/wms/erenRetail/inbound/validate-scanned-box')
            'data-url' => Url::to('/intermode/inbound/scanning/validate-scanned-box')
        ]
    ); ?>

    <?= $form->field($inboundForm, 'product_barcode',
        ['labelOptions' => ['label' => Yii::t('inbound/forms', 'Product Barcode')],
            'template' => "{label}\n{input-group-begin}{input}{button-right}{input-group-end}\n{hint}\n{error}\n",
            'parts' => [
                '{label}' => '<label for="inboundform-box_barcode">' . Yii::t('inbound/forms', 'Product Barcode') . '</label>',
                '{input-group-begin}' => '<div class="input-group">',
                '{input-group-end}' => '</div>',
                '{button-right}' => '<div class="input-group-addon" style="background-color: none; border: none; border-radius: none;" ><span class="btn btn-success btn-xs" data-url-value="' . Url::toRoute(['clear-product-in-box']) . '" id="clear-product-in-box-by-one-bt">' . Yii::t('inbound/buttons', 'Clear product in box') . '</span></div>'
            ]
//        ]
		])->textInput([
            'id' => 'inbound-form-product_barcode',
            'class' => 'form-control input-lg',
//            'data-url' => Url::to('/wms/erenRetail/inbound/scan-product-in-box')
            'data-url' => Url::to('/intermode/inbound/scanning/scan-product-in-box')
        ]); ?>

	<?= $form->field($inboundForm, 'datamatrix')->textInput([
		'class' => 'form-control ext-large-input',
		'data-url' => Url::to('scan-datamatrix'),
	]); ?>

	<?= $form->field($inboundForm, 'stockId')->hiddenInput()->label(false)->error(false); ?>

    <?php ActiveForm::end(); ?>


    <div class="form-group">
<!--        --><?//= Html::tag('span', Yii::t('inbound/buttons', 'Accept').'<span id="inbound-messages-process"> </span>', ['class' => 'btn btn-danger pull-right', 'data-url' => Url::toRoute('confirm-order'), 'style' => ' margin-left:10px;', 'id' => 'inbound-accept-bt']) ?>
        <?= Html::tag('span', Yii::t('inbound/buttons', 'List differences'), ['data-url' => Url::toRoute('print-list-differences'), 'class' => 'btn btn-success', 'id' => 'inbound-list-differences-bt', 'style' => 'margin-left:10px;']) ?>
        <?= Html::tag('span', Yii::t('inbound/buttons', 'Unallocated box'), ['data-url' => Url::toRoute('print-unallocated-list'), 'class' => 'btn btn-primary', 'id' => 'inbound-unallocated-list-bt', 'style' => 'margin-right:10px;']) ?>
    </div>
    <div id="countdown" data-on="0"></div>
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
    <div id="inbound-items" class="table-responsive">
        <table class="table">
            <tr>
                <th><?= Yii::t('inbound/forms', 'Product Barcode'); ?></th>
                <th><?= Yii::t('inbound/forms', 'Product Model'); ?></th>
                <th><?= Yii::t('inbound/forms', 'Expected Qty'); ?></th>
                <th><?= Yii::t('inbound/forms', 'Accepted Qty'); ?></th>
            </tr>
            <tbody id="inbound-item-body"></tbody>
        </table>
    </div>
	        <?= Html::tag('span', Yii::t('inbound/buttons', 'Accept').'<span id="inbound-messages-process"> </span>', ['class' => 'btn btn-danger pull-right', 'data-url' => Url::toRoute('/intermode/inbound/scanning/confirm-order'), 'style' => ' margin-left:10px;', 'id' => 'inbound-accept-bt']) ?>
</div>
