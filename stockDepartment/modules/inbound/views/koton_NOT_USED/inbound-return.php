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
//use stockDepartment\modules\inbound\assets\InboundAsset;
//InboundAsset::register($this);
/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $orderNumberArray common\modules\inbound\models\InboundOrder */
/* @var $clientsArray common\modules\client\models\Client */
/* @var $inboundForm stockDepartment\modules\inbound\models\InboundForm */
?>

<?php $this->title = Yii::t('inbound/titles', 'Koton Inbound Return'); ?>
<h1><?= $this->title ?></h1>
<div class="order-process-form">
    <?php $form = ActiveForm::begin([
            'id' => 'inbound-return-form',
            'enableClientValidation' => false,
            'validateOnChange' => false,
            'validateOnSubmit' => false,
            'options' => [
                'data-printType' => \Yii::$app->params['printType'],
            ]
        ]
    ); ?>

    <?= $form->field($inboundForm, 'client_id', ['labelOptions' => ['label' => Yii::t('inbound/forms', 'Client ID')]])->dropDownList(
        $clientsArray,
        ['prompt' => '',
            'id' => 'inbound-return-form-client-id',
            'class' => 'form-control input-lg',
            'readonly' => true,
        ]
    ); ?>

    <?= $form->field($inboundForm, 'product_barcode')->textInput([
            'id' => 'inbound-return-form-product_barcode',
            'class' => 'form-control input-lg',
            'data-url' => Url::toRoute('scan-product-barcode')
        ]); ?>
    <?= $form->field($inboundForm, 'inbound_order_number',['labelOptions' => ['label' => false]])->hiddenInput([
        'id' => 'inbound-return-form-order-number',
    ]); ?>
    <?= $form->field($inboundForm, 'accepted_qty',['labelOptions' => ['label' => false]])->hiddenInput([
        'id' => 'inbound-return-form-accepted-qty',
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

    <div id="inbound-return-items" class="table-responsive">
        <table class="table">
            <tr>

                <th><?= Yii::t('inbound/forms', 'Party number'); ?></th>
                <th><?= Yii::t('inbound/forms', 'Order number'); ?></th>
                <th><?= Yii::t('inbound/forms', 'Product Barcode'); ?></th>
                <th><?= Yii::t('inbound/forms', 'Expected Qty'); ?></th>
                <th><?= Yii::t('inbound/forms', 'Accepted Qty'); ?></th>
                <th><?= Yii::t('inbound/forms', 'Difference Qty'); ?></th>
                <th> </th>
            </tr>
            <tbody id="inbound-return-item-body"></tbody>
        </table>
    </div>
</div>
<iframe style="display: none" name="frame-print-alloc-list" src="#" width="468" height="468">
</iframe>
<script type="text/javascript">
    $(function (){
        var inboundReturnManager = function () {
            var inboundReturnForm = $('#inbound-return-form'),
                inboundReturnItems = $('#inbound-return-items'),
                productBarcode = $('#inbound-return-form-product_barcode'),
                inboundOrderId = $('#inbound-return-form-order-number'),
                acceptedQty = $('#inbound-return-form-accepted-qty'),
                me;
            $('body').on('click', '#inbound-return-accept-bt', function(e){

            });
            return me = {
                'onLoad' : function () {
                    console.info('-inbound-return init-');
                    inboundReturnForm.submit(function(e){
                        e.preventDefault(e);
                    });
                    inboundReturnItems.hide();
                    productBarcode.focus();
                },
                'resetHiddenField' : function(){
                    acceptedQty.val('');
                    inboundOrderId.val('');
                },
                'scanBarcodeHandler' : function (event) {
                    event.preventDefault();
                    errorBase.setForm(inboundReturnForm);
                    acceptedQty.val('');
                    inboundOrderId.val('');
                    if (event.which == 13){
                        console.log('scan product');
                        var url = $(this).data('url');

                        $.post(url, inboundReturnForm.serialize()).done(function (result) {
                            $('#accepted-qty').val('');
                            inboundOrderId.val('');
                            if (result.success == 0 ) {
                                errorBase.eachShow(result.errors);
                                productBarcode.focus().select();
                            } else {
                                errorBase.hidden();
                                productBarcode.focus().select();
                                inboundReturnItems.show();
                                $('#inbound-return-item-body').html(result.renderData);
                            }

                        }).fail(function () {
                            console.log("server error");
                        });

                    }
                },
                'acceptButtonHandler' : function (event) {
                    event.preventDefault();
                    errorBase.setForm(inboundReturnForm);
                        console.log('click accept button');

                        var button = $(this),
                            url = button.data('url');

                        inboundOrderId.val(button.data('product-barcode'));
                        acceptedQty.val($('#accepted-qty').val());

                        $.post(url, inboundReturnForm.serialize()).done(function (result) {
                            if (result.success == 0 ) {
                                errorBase.eachShow(result.errors);
                                acceptedQty.focus().select();
                            } else {
                                errorBase.hidden();
                                acceptedQty.val('');
                                inboundOrderId.val('');
                                inboundReturnItems.show();
                                $('#inbound-return-item-body').html(result.renderData);
                            }

                        }).fail(function () {
                            console.log("server error");
                        });
                }
            }
        };

        var inboundReturnModel = inboundReturnManager();
        inboundReturnModel.onLoad();
        $('body').on('keyup', '#inbound-return-form-product_barcode', inboundReturnModel.scanBarcodeHandler);
        //$('body').on('click', '.inbound-return-accept-bt', inboundReturnModel.acceptButtonHandler);

    });

</script>