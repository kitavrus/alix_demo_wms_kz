<?php

use app\modules\ecommerce\assets\intermode\ScanInboundFormAsset;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $orderNumberArray common\modules\inbound\models\InboundOrder */
/* @var $clientsArray common\modules\client\models\Client */
/* @var $inboundForm stockDepartment\modules\inbound\models\InboundForm */


ScanInboundFormAsset::register($this);

?>
<?php $this->title = "Поступление Intermode Ecommerce"; ?>
<h1><?= $this->title ?></h1>

<div class="scan-inbound-form">
    <?php $form = ActiveForm::begin([
            'id' => 'scaninboundform',
            'enableClientValidation' => false,
            'validateOnChange' => false,
            'validateOnSubmit' => false,
        ]
    ); ?>

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


    <?= $form->field($inboundForm, 'orderNumberId',
        ['template' => "{label}\n{input-group-begin}{counter}{input}{input-group-end}\n{hint}\n{error}\n",
            'parts' => [
                '{input-group-begin}' => '<div class="input-group">',
                '{input-group-end}' => '</div>',
                '{counter}' => '<div class="input-group-addon" style="font-size: 42px;" >' . Yii::t('inbound/titles', 'Products') . ': <strong id="count-products-in-order" >0</strong>&nbsp;&nbsp;&nbsp;&nbsp;<span class="in-order">' . Yii::t('inbound/titles', 'In order') . ': </span></div>',

            ]
        ]
    )->dropDownList(
        $newAndInProcessOrders,
        ['prompt' => '',
            'class' => 'form-control ext-large-input',
            'data-url' => Url::to('select-order-number')
        ]
    ); ?>

    <?= $form->field($inboundForm, 'ourBoxBarcode',[
            'template' => "{label}\n{input-group-begin}{counter}{input}{input-group-end}\n{hint}\n{error}\n",
            'parts' => [
                '{input-group-begin}' => '<div class="input-group">',
                '{input-group-end}' => '</div>',
                '{counter}' => '<div class="input-group-addon" style="font-size: 22px; background-color: lightblue;">Тов.пр:<strong id="our-box-prod-acc" >0</strong></div>',
            ]
        ])->textInput(
        [
            'class' => 'form-control ext-large-input',
            'data-url' => Url::to('scan-our-box-barcode')
        ]
    ); ?>

    <?= $form->field($inboundForm, 'productBarcode')->textInput([
        'class' => 'form-control ext-large-input',
        'data-url' => Url::to('scan-product-barcode'),
        'data-clean-url' => Url::to('clean-our-box')
    ]); ?>

	<?= $form->field($inboundForm, 'datamatrix')->textInput([
        'class' => 'form-control ext-large-input',
        'data-url' => Url::to('scan-datamatrix'),
    ]); ?>

	<?= $form->field($inboundForm, 'stockId')->hiddenInput()->label(false)->error(false); ?>

    <?= $form->field($inboundForm, 'conditionType')->dropDownList($conditionTypeArray,[
        'class' => 'form-control input-lg',
    ]); ?>

    <?php ActiveForm::end(); ?>

    <div class="form-group">
        <?= Html::tag('span', Yii::t('inbound/buttons', 'List differences'), ['data-url' => Url::toRoute('print-diff-in-order'), 'class' => 'btn btn-success', 'id' => 'scaninboundform-print-diff-in-order-bt', 'style' => 'margin-left:10px;']) ?>
        <?= Html::tag('span', Yii::t('inbound/buttons', 'Содержимое заказа'), ['data-url' => Url::toRoute('show-order-items'), 'class' => 'btn btn-primary', 'id' => 'scaninboundform-inbound-show-items-bt', 'style' => 'margin-right:10px;']) ?>
        <?= Html::tag('span', Yii::t('inbound/buttons', 'Unallocated box'), ['data-url' => Url::toRoute('print-unallocated-list'), 'class' => 'btn btn-primary', 'id' => 'scaninboundform-unallocated-list-bt', 'style' => 'margin-right:10px;']) ?>
<!--		--><?//= Html::tag('span', Yii::t('inbound/buttons', 'Проверка'), ['data-url' => Url::toRoute('check-order'), 'class' => 'btn btn-danger pull-right', 'id' => 'scaninboundform-check-order-bt', 'style' => 'margin-right:10px;']) ?>
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
    <div id="inbound-items" class="table-responsive"></div>
</div>

<?= Html::tag('span',
	Yii::t('inbound/buttons', 'Accept').'<span id="inbound-close-message-process"> </span>',
	['class' => 'btn btn-danger pull-right',
		'data-url' => Url::toRoute('done-order'),
		'style' => 'margin-top: 42px; float:right; font-size: 25px; display:none;',
		'id' => 'scaninboundform-inbound-close-bt'])
?>

<script type="text/javascript">
    $(function () {
        $('#scaninboundform-unallocated-list-bt').on('click', function () {
            var href = $(this).data('url');
            window.location.href = href + '?inbound_id=' + $('#scaninboundform-ordernumberid').val();
        });
    });
</script>