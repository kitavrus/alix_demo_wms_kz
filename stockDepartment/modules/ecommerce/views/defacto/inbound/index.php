<?php
/**
 * Created by PhpStorm.
 * User: Igor
 * Date: 08.01.15
 * Time: 7:02
 */

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

//\stockDepartment\modules\wms\assets\carParts\main\ScanInboundFormAsset::register($this);
\app\modules\ecommerce\assets\defacto\ScanInboundFormAsset::register($this);

?>
<?php $this->title = "Обработка приходной накладной для Defacto Ecommerce"; ?>
<h1><?= $this->title ?></h1>

<div class="scan-inbound-form">
    <?php $form = ActiveForm::begin([
            'id' => 'scaninboundform',
            'enableClientValidation' => false,
            'validateOnChange' => false,
            'validateOnSubmit' => false,
//            'options' => [
//                'data-printType' => \Yii::$app->params['printType']
//            ]
        ]
    ); ?>
	
    <?= Html::tag('span',
        Yii::t('inbound/buttons', 'Accept').'<span id="inbound-close-message-process"> </span>',
        ['class' => 'btn btn-danger pull-right',
            'data-url' => Url::toRoute('done-order'),
            'style' => 'margin-top:-42px; float:right; font-size: 25px; display:block;',
            'id' => 'scaninboundform-inbound-close-bt'])
    ?>

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

    <?= $form->field($inboundForm, 'clientBoxBarcode', [
        'template' => "{label}\n{input-group-begin}{counter}{input}{input-group-end}\n{hint}\n{error}\n",
        'parts' => [
            '{input-group-begin}' => '<div class="input-group">',
            '{input-group-end}' => '</div>',
            '{counter}' => '<div class="input-group-addon" style="font-size: 22px; background-color: lightblue;">Тов.ож:<strong id="client-box-prod-exp" >0</strong>&nbsp;&nbsp;Тов.пр:<strong id="client-box-prod-acc" >0</strong></div>',
        ]
    ])->textInput(
        [
            'class' => 'form-control ext-large-input',
            'data-url' => Url::to('scan-client-box-barcode')
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

<!--    --><?//= $form->field($inboundForm, 'lotBarcode', [
//        'template' => "{label}\n{input-group-begin}{counter}{input}{input-group-end}\n{hint}\n{error}\n",
//        'parts' => [
//            '{input-group-begin}' => '<div class="input-group">',
//            '{input-group-end}' => '</div>',
//            '{counter}' => '<div class="input-group-addon" style="font-size: 22px; background-color: lightyellow;"" >Тов.ож:<strong id="product-qty-in-lot-expected" >0</strong>&nbsp;&nbsp;Тов.пр:<strong id="product-qty-in-lot-accepted" >0</strong></div>',
//        ]
//    ])->textInput(
//        [
//            'class' => 'form-control ext-large-input',
//            'data-url' => Url::to('scan-lot-barcode')
//        ]
//    ); ?>

    <?= $form->field($inboundForm, 'productBarcode',
        [
        'template' => "{label}\n{input-group-begin}{counter}{input}{input-group-end}\n{hint}\n{error}\n",
        'parts' => [
            '{input-group-begin}' => '<div class="input-group">',
            '{input-group-end}' => '</div>',
            '{counter}' => '<div class="input-group-addon" style="font-size: 22px; background-color: greenyellow;" >Тов. принято:<strong id="product-scanned-in-lot" >0</strong></div>',
        ]
    ]
        )->textInput([
        'class' => 'form-control ext-large-input',
        'data-url' => Url::to('scan-product-barcode'),
        'data-clean-url' => Url::to('clean-our-box')
    ]); ?>

    <?= $form->field($inboundForm, 'conditionType')->dropDownList($conditionTypeArray,[
        'class' => 'form-control input-lg',
    ]); ?>

    <?php ActiveForm::end(); ?>

    <div class="form-group">
        <?= Html::tag('span', Yii::t('inbound/buttons', 'List differences'), ['data-url' => Url::toRoute('print-diff-in-order'), 'class' => 'btn btn-success', 'id' => 'scaninboundform-print-diff-in-order-bt', 'style' => 'margin-left:10px;']) ?>
        <?= Html::tag('span', Yii::t('inbound/buttons', 'Содержимое заказа'), ['data-url' => Url::toRoute('show-order-items'), 'class' => 'btn btn-primary', 'id' => 'scaninboundform-inbound-show-items-bt', 'style' => 'margin-right:10px;']) ?>
        <?= Html::tag('span', Yii::t('inbound/buttons', 'Unallocated box'), ['data-url' => Url::toRoute('print-unallocated-list'), 'class' => 'btn btn-primary', 'id' => 'scaninboundform-unallocated-list-bt', 'style' => 'margin-right:10px;']) ?>
		<?= Html::tag('span', Yii::t('inbound/buttons', 'Проверка'), ['data-url' => Url::toRoute('check-order'), 'class' => 'btn btn-danger pull-right', 'id' => 'scaninboundform-check-order-bt', 'style' => 'margin-right:10px;']) ?>
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

<script type="text/javascript">
    $(function () {
        $('#scaninboundform-unallocated-list-bt').on('click', function () {
            var href = $(this).data('url');
            window.location.href = href + '?inbound_id=' + $('#scaninboundform-ordernumberid').val();
        });
    });
</script>