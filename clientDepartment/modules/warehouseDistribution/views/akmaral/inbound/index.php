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
use common\modules\store\models\Store;
use common\modules\transportLogistics\components\TLHelper;
use app\modules\returnOrder\assets\ReturnAsset;
/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model app\modules\returnOrder\models\ReturnForm */
?>
<?php ReturnAsset::register($this); ?>

<?php $this->title = Yii::t('return/titles', 'Return Orders'); ?>
<div id="messages-container">
    <div id="messages-base-line"></div>
<!--    --><?//= Alert::widget([
//        'options' => [
//            'id' => 'messages-list',
//            //'class' => 'alert-info hidden',
//        ],
//        'body' => '<span id="messages-list-body"></span>',
//    ]);
//    ?>
</div>
<div class="return-order-process-form">
    <?php $form = ActiveForm::begin([
            'id' => 'return-process-form',
//            'enableClientValidation' => false,
            'validateOnChange' => false,
//            'validateOnSubmit' => false,
            'options' => ['enctype' => 'multipart/form-data']
        ]
    ); ?>

<!--    --><?php //= $form->field($model, 'store_id')->dropDownList($filterWidgetOptionDataRoute, ['prompt'=>Yii::t('return/titles', 'Select store'),'data'=>['url'=>Url::toRoute('get-inbound-orders-number')]]); ?>

    <?= $form->field($model,'inbound_order_number')->dropDownList($inboundOrderNumberList, ['prompt'=>Yii::t('return/titles', 'Select order'),'data'=>['url'=>Url::toRoute('get-orders-items-by-party-id')]]); ?>
    <?= Html::tag('span', Yii::t('return/buttons', 'Generate new'), ['data-url' => Url::toRoute('get-inbound-orders-number'), 'class' => 'btn btn-success', 'id' => 'inboundAkmaral-process-form-generate-new-bt', 'style' => 'margin-left:10px;']) ?>
    <?= Html::tag('span', Yii::t('return/buttons', 'Delete order'), ['data-url' => Url::toRoute('delete-inbound-order'), 'class' => 'btn btn-danger hidden', 'id' => 'inboundAkmaral-delete-inbound-order-bt', 'style' => 'margin-right:10px;']) ?>
    <?= Html::tag('span', Yii::t('return/buttons', 'Accept inbound order'), ['data-url' => Url::toRoute('accept-inbound-order'), 'class' => 'btn btn-warning pull-right', 'id' => 'inboundAkmaral-process-form-accept-inbound-order-bt', 'style' => 'margin-right:10px;']) ?>

<br />
<br />
    <?= $form->field($model, 'file[]')->fileInput(['multiple' => true]) ?>

    <div class="row" style="margin: 20px 1px">
        <?= Html::submitButton(Yii::t('return/buttons', 'Upload').'<span id="inboundAkmaral-process-form-upload-message"></span>', ['class'=>'btn btn-primary','id'=>'inboundAkmaral-process-form-upload-submit-bt']) ?>
    </div>
    <?php ActiveForm::end(); ?>
    <div id="error-container">
        <div id="error-base-line"></div>
<!--        --><?//= Alert::widget([
//            'options' => [
//                'id' => 'error-list',
//                'class' => 'alert-danger hidden',
//            ],
//            'body' => '',
//        ]);
//        ?>
    </div>
    <div id="inbound-items" class="table-responsive">
        <table class="table">
            <tr>
                <th><?= Yii::t('return/forms', 'Box Barcode'); ?></th>
                <th><?= Yii::t('return/forms', 'Expected Qty'); ?></th>
                <th>-</th>
            </tr>
            <tbody id="inbound-item-body"></tbody>
        </table>
    </div>
</div>
<script type="text/javascript">
    $(function() {
        console.info('INIT inbound JS');
        var b = $('body'),
            /*store = $("#akmaralinboundform-store_id"),*/
            order = $("#akmaralinboundform-inbound_order_number");

        if(/*store.val() &&*/ order.val()) {
            var url = order.data('url'),
                party_id = order.val();

            if(party_id) {
                $.post(url, {'party_id': party_id})
                    .done(function (result) {
                        $('#inbound-item-body').html(result.items);
                    })
                    .fail(function () {
                        console.log("server error");
                    });
            } else {
                $('#inbound-item-body').html('');
            }
        }

        b.on('click', '#inboundAkmaral-process-form-generate-new-bt', function () {

            if (confirm('Вы действительно хотите сгенерировать новую накладную')) {

                var me = $(this),
                    url = me.data('url'),
                    store_id = 0;
                /*store_id = $('#returnform-store_id').val()*/
                /* if (store_id) {*/
                    $.post(url, { 'generate_new': true})
                        .done(function (result) {

                            $('#akmaralinboundform-inbound_order_number').html('');

                            $.each(result.data_options, function (key, value) {
                                $('#akmaralinboundform-inbound_order_number').append('<option value="' + key + '">' + value + '</option>');
                            });
                        })
                        .fail(function () {
                            console.log("server error");
                        });

/*                } else {
                    alert("Пожалуйста выберите магазин");
                }*/
            }

        });

        b.on('click', '#inboundAkmaral-process-form-accept-inbound-order-bt', function () {

            if (confirm('Вы действительно хотите отправить накладную на обработку складу')) {

                var me = $(this),
                    url = me.data('url'),
                    /*store_id = $('#returnform-store_id').val(),*/
                    party_id = $('#akmaralinboundform-inbound_order_number').val();

                if (/*store_id &&*/ party_id) {

                    $.post(url, {/*'store_id': store_id,*/'party_id':party_id})
                        .done(function (result) {
                            if(result.errors == 0) {
                                $('#akmaralinboundform-inbound_order_number').html('');

                                $.each(result.data_options, function (key, value) {
                                    $('#akmaralinboundform-inbound_order_number').append('<option value="' + key + '">' + value + '</option>');
                                });
                                window.location.href = '/warehouseDistribution/akmaral/inbound/index';
                            } else {
                                window.location.href = '/warehouseDistribution/akmaral/inbound/index?&party_id='+result.party_id;
                            }

                        })
                        .fail(function () {
                            console.log("server error");
                        });

                } else {
                    alert("Пожалуйста выберите магазин и накладную");
                }
            }
        });

    });
</script>