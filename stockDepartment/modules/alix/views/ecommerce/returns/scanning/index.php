<?php

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $inboundForm stockDepartment\modules\alix\controllers\ecommerce\returns\domain\ReturnScanningForm */
/* @var $partyNumberArray array */
/* @var $items string */
/* @var $expected_qty int */
/* @var $accepted_qty int */

$this->title = Yii::t('inbound/titles', 'Обработка возвратов');
?>
<h1><?= Html::encode($this->title) ?></h1>

<div class="order-process-form">
    <?php $form = ActiveForm::begin([
        'id' => 'inbound-process-form',
        'enableClientValidation' => false,
        'validateOnChange' => false,
        'validateOnSubmit' => false,
        'options' => ['data-printType' => \Yii::$app->params['printType']],
    ]); ?>

    <?= Html::activeHiddenInput($inboundForm, 'client_id', ['id' => 'main-form-client-id']) ?>

    <?= $form->field($inboundForm, 'order_number', [
        'template' => "{label}\n{input-group-begin}{counter}{input}{input-group-end}\n{hint}\n{error}\n",
        'parts' => [
            '{label}' => '<label for="inbound-form-order-number">' . Yii::t('inbound/forms', 'Order number') . '</label>',
            '{input-group-begin}' => '<div class="input-group">',
            '{input-group-end}' => '</div>',
            '{counter}' => '<div class="input-group-addon" >' . Yii::t('inbound/titles', 'Products') . ': <strong id="count-products-in-order" >' . (int)$accepted_qty . ' / ' . (int)$expected_qty . '</strong>&nbsp;&nbsp;&nbsp;&nbsp;<span class="in-order">' . Yii::t('inbound/titles', 'In order') . ': </span></div>',
        ],
    ])->dropDownList($partyNumberArray, [
        'id' => 'inbound-form-order-number',
        'class' => 'form-control input-lg',
        'data-url' => Url::to(['/alix/ecommerce/returns/scanning/get-scanned-product-by-id']),
    ]); ?>

    <?= $form->field($inboundForm, 'box_barcode', [
        'template' => "{label}\n{input-group-begin}{counter}{input}{button-right}{input-group-end}\n{hint}\n{error}\n",
        'parts' => [
            '{label}' => '<label for="inboundform-box_barcode">' . Yii::t('inbound/forms', 'Box Barcode') . '</label>',
            '{input-group-begin}' => '<div class="input-group">',
            '{input-group-end}' => '</div>',
            '{counter}' => '<div class="input-group-addon" >' . Yii::t('inbound/titles', 'Products') . ': <strong id="count-product-in-box" >0</strong>&nbsp;&nbsp;&nbsp;&nbsp;<span class="in-box">' . Yii::t('inbound/titles', 'In box') . ': </span></div>',
            '{button-right}' => '<div class="input-group-addon" style="background-color: none; border: none; border-radius: none;" ><span class="btn btn-success btn-xs" data-url-value="' . Url::toRoute(['clear-box']) . '" id="clear-box-bt">' . Yii::t('inbound/buttons', 'Clear Box') . '</span></div>',
        ],
    ])->textInput([
        'id' => 'inbound-form-box_barcode',
        'class' => 'form-control input-lg',
        'data-url' => Url::to('/alix/ecommerce/returns/scanning/validate-scanned-box'),
    ]); ?>

    <?= $form->field($inboundForm, 'product_barcode', [
        'labelOptions' => ['label' => Yii::t('inbound/forms', 'Product Barcode')],
        'template' => "{label}\n{input-group-begin}{input}{button-right}{input-group-end}\n{hint}\n{error}\n",
        'parts' => [
            '{label}' => '<label for="inboundform-box_barcode">' . Yii::t('inbound/forms', 'Product Barcode') . '</label>',
            '{input-group-begin}' => '<div class="input-group">',
            '{input-group-end}' => '</div>',
            '{button-right}' => '<div class="input-group-addon" style="background-color: none; border: none; border-radius: none;" ><span class="btn btn-success btn-xs" data-url-value="' . Url::toRoute(['clear-product-in-box']) . '" id="clear-product-in-box-by-one-bt">' . Yii::t('inbound/buttons', 'Clear product in box') . '</span></div>',
        ],
    ])->textInput([
        'id' => 'inbound-form-product_barcode',
        'class' => 'form-control input-lg',
        'data-url' => Url::to('/alix/ecommerce/returns/scanning/scan-product-in-box'),
    ]); ?>

    <div class="form-group">
        <?= Html::tag('span', Yii::t('inbound/buttons', 'List differences'), ['data-url' => Url::toRoute('print-list-differences'), 'class' => 'btn btn-success', 'id' => 'inbound-list-differences-bt']) ?>
        <?= Html::tag('span', Yii::t('inbound/buttons', 'Unallocated box'), ['data-url' => Url::toRoute('print-unallocated-list'), 'class' => 'btn btn-primary', 'id' => 'inbound-unallocated-list-bt', 'style' => 'margin-left:10px;']) ?>
    </div>

    <div id="error-container">
        <div id="error-base-line"></div>
        <?= Alert::widget([
            'options' => ['id' => 'error-list', 'class' => 'alert-danger hidden'],
            'body' => '',
        ]); ?>
    </div>

    <div id="inbound-items" class="table-responsive">
        <table class="table">
            <tr>
                <th><?= Yii::t('inbound/forms', 'Product Barcode'); ?></th>
                <th><?= Yii::t('inbound/forms', 'Expected Qty'); ?></th>
                <th><?= Yii::t('inbound/forms', 'Accepted Qty'); ?></th>
            </tr>
            <tbody id="inbound-item-body"><?= $items ?></tbody>
        </table>
    </div>

    <?= Html::tag('span', Yii::t('inbound/buttons', 'Accept') . '<span id="inbound-messages-process"> </span>', ['class' => 'btn btn-danger pull-right', 'data-url' => Url::toRoute('confirm-order'), 'style' => 'margin-left:10px;', 'id' => 'inbound-accept-bt']) ?>

    <?php ActiveForm::end(); ?>
</div>

<script type="application/javascript">
    $(function () {
        $("#inbound-form-box_barcode").focus().select();

        $('#inbound-form-box_barcode, #inbound-form-product_barcode').on('click', function () {
            $(this).focus().select();
        });

        $('#inbound-form-box_barcode').on('keyup', function (e) {
            if (e.which !== 13) { return; }
            var me = $(this), form = $('#inbound-process-form'), url = $(this).data('url');
            errorBase.setForm(form);
            me.focus().select();
            var data = 'ReturnScanningForm[client_id]=' + $('#main-form-client-id').val() + "&" + form.serialize();
            $.post(url, data, function (result) {
                if (result.success == 0) { errorBase.eachShow(result.errors); me.focus().select(); }
                else { errorBase.hidden(); $("#inbound-form-product_barcode").focus().select(); $('#count-product-in-box').html(result.countProductInBox); }
            }, 'json');
            e.preventDefault();
        });

        $('#inbound-form-product_barcode').on('keyup', function (e) {
            if (e.which !== 13) { return; }
            var me = $(this), url = $(this).data('url');
            me.focus().select();
            if (me.val() === 'CHANGEBOX') { $('#inbound-form-box_barcode').focus().select(); me.val(''); return true; }
            var form = $('#inbound-process-form');
            var data = 'ReturnScanningForm[client_id]=' + $('#main-form-client-id').val() + "&" + form.serialize();
            errorBase.setForm(form);
            $.post(url, data, function (result) {
                if (result.success == 0) { errorBase.eachShow(result.errors); me.focus().select(); }
                else {
                    errorBase.hidden();
                    $('#count-product-in-box').html(result.countProductInBox);
                    $('#count-products-in-order').html(result.countScannedProductInOrder + ' / ' + result.expected_qty);
                    $('#inbound-item-body').html(result.items);
                }
            }, 'json');
            e.preventDefault();
        });

        $('#inbound-list-differences-bt').on('click', function () {
            var href = $(this).data('url'),
                printType = $('#inbound-process-form').data('printtype'),
                id = $('#inbound-form-order-number').val();
            if (!id) return;
            if (printType === 'pdf') { window.location.href = href + '?inbound_id=' + id; }
            else if (printType === 'html') { autoPrintAllocatedListHtml(href + '?inbound_id=' + id, '0', 2500); }
        });

        $('#inbound-unallocated-list-bt').on('click', function () {
            var href = $(this).data('url'),
                printType = $('#inbound-process-form').data('printtype'),
                id = $('#inbound-form-order-number').val();
            if (!id) return;
            if (printType === 'pdf') { window.location.href = href + '?inbound_id=' + id; }
            else if (printType === 'html') { autoPrintAllocatedListHtml(href + '?inbound_id=' + id); }
        });

        $('#clear-box-bt').on('click', function () {
            var href = $(this).data('url-value'), form = $('#inbound-process-form');
            errorBase.setForm(form);
            if (confirm('Вы действительно хотите очистить короб')) {
                $.post(href, form.serialize(), function (result) {
                    errorBase.hidden();
                    $('#count-product-in-box').html('0');
                    $('#count-products-in-order').html(result.countScannedProductInOrder + ' / ' + result.expected_qty);
                    $('#inbound-item-body').html(result.items);
                }, 'json');
            }
        });

        $('#clear-product-in-box-by-one-bt').on('click', function () {
            var href = $(this).data('url-value'), form = $('#inbound-process-form');
            errorBase.setForm(form);
            $.post(href, form.serialize(), function (result) {
                errorBase.hidden();
                $('#count-product-in-box').html(result.countProductInBox);
            }, 'json');
        });

        $('#inbound-accept-bt').on('click', function () {
            var client_idValue = $('#main-form-client-id').val(),
                order_numberValue = $('#inbound-form-order-number').val(),
                messages_processText = $('#inbound-messages-process'),
                form = $('#inbound-process-form'),
                url = $(this).data('url');
            if (!confirm('Вы уверены, что хотите закрыть возврат')) return;
            if (!client_idValue || !order_numberValue) return;
            $(messages_processText).html(' Подождите, идет обработка ...');
            var data = 'ReturnScanningForm[client_id]=' + client_idValue + "&" + form.serialize();
            $.post(url, data).done(function (result) {
                var alertMessage = '';
                $.each(result.messages, function (key, value) { if (value && value.length) { alertMessage += value + '\n'; } });
                alert(alertMessage);
                window.location.href = '/alix/ecommerce/returns/scanning/index';
            });
        });
    });
</script>
