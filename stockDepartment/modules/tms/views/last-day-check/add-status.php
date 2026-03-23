<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 12.01.2019
 * Time: 15:45
 */

use common\modules\transportLogistics\components\TLHelper;
use yii\helpers\Html;
use app\modules\transportLogistics\transportLogistics;
use kartik\widgets\ActiveForm;

use yii\helpers\ArrayHelper;
use common\modules\transportLogistics\models\TlAgents;
use common\modules\transportLogistics\models\TlCars;
use kartik\detail\DetailView;
use common\modules\client\models\Client;

/* @var $this yii\web\View
/* @var $addStatusForm common\components\FailDeliveryStatus\AddStatusForm */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tl-delivery-proposal-form">

    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($addStatusForm, 'status')->dropDownList(\common\components\FailDeliveryStatus\StatusList::getList(),['prompt' =>Yii::t('transportLogistics/titles', 'Select client')]); ?>
    <?= $form->field($addStatusForm, 'otherStatus')->textarea(['style'=>'display:none'])->label(null,['style'=>'display:none']); ?>
    <?= $form->field($addStatusForm, 'deliveryProposalId')->hiddenInput()->label(false) ?>

    <div class="form-group">
        <?= Html::submitButton( Yii::t('transportLogistics/buttons', 'ADD'), ['class' => 'btn btn-success']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>


<script type="application/javascript">

    $(function() {

        $('#addstatusform-status').on('change',function(e) {
            console.log();
            var currentStatus = $('#addstatusform-status option:selected').val();
            var labelOtherStatus = $('label[for=addstatusform-otherstatus]');
            var inputOtherStatus = $('#addstatusform-otherstatus');

            if(currentStatus == '<?php echo \common\components\FailDeliveryStatus\StatusList::OTHER_REASON ?>') {
                labelOtherStatus.show();
                inputOtherStatus.show();
            } else {
                labelOtherStatus.hide();
                inputOtherStatus.hide();
            }
        })
    })

</script>

<div>
<?=
DetailView::widget([
    'model' => $deliveryProposal,
    'attributes' => [
        [
            'attribute' => 'client_id',
            'type' => DetailView::INPUT_SELECT2,
            'widgetOptions' => [
                'data' => ArrayHelper::map(Client::findAll(['status' => Client::STATUS_ACTIVE]), 'id', 'title'),
            ],
            'value' => is_object($deliveryProposal->client) ? $deliveryProposal->client->title : Yii::t('titles', 'Not set'),

        ],
        [
            'type' => DetailView::INPUT_DROPDOWN_LIST,
            'items' => $deliveryProposal::getCompanyTransporterArray(),
            'attribute' => 'company_transporter',
            'value' => $deliveryProposal->getCompanyTransporterValue(),

        ],
        'seal',
        [
            'type' => DetailView::INPUT_DROPDOWN_LIST,
            'items' => $deliveryProposal::getDeliveryTypeArray(),
            'attribute' => 'delivery_type',
            'value' => $deliveryProposal->getDeliveryTypeValue(),

        ],
        [
            'type' => DetailView::INPUT_DROPDOWN_LIST,
            'items' => $deliveryProposal::getDeliveryMethodArray(),
            'attribute' => 'delivery_method',
            'value' => $deliveryProposal->getDeliveryMethod(),

        ],
        [
            'attribute' => 'route_from',
            'type' => DetailView::INPUT_SELECT2,
            'widgetOptions' => [
                'data' => TLHelper::getStockPointArray(),
            ],
            'value' => isset($storeArray[$deliveryProposal->route_from]) ? $storeArray[$deliveryProposal->route_from] : Yii::t('titles', 'Not set'),
        ],
        [
            'type' => DetailView::INPUT_SELECT2,
            'widgetOptions' => [
                'data' => TLHelper::getStockPointArray(),
            ],
            'attribute' => 'route_to',
            'value' => isset($storeArray[$deliveryProposal->route_to]) ? $storeArray[$deliveryProposal->route_to] : Yii::t('titles', 'Not set'),
        ],
        [
            'attribute' => 'delivery_date',
            'displayOnly' => (!empty($deliveryProposal->delivery_date) ? true : false),
            'format' => 'datetime',
        ],
        [
            'attribute' => 'expected_delivery_date',
            'format' => 'datetime',
        ],
        [
            'attribute' => 'accepted_datetime',
            'format' => 'datetime',
        ],
        [
            'attribute' => 'shipped_datetime',
            'format' => 'datetime',
        ],
        'mc:decimal',

        'mc_actual',
        'kg',
        'kg_actual',
        'volumetric_weight',
        'number_places',
        'number_places_actual',
        [
            'displayOnly' => true,
            'type' => DetailView::INPUT_DROPDOWN_LIST,
            'items' => TlAgents::getActiveAgentsArray(),
            'attribute' => 'agent_id',
            'value' => TlAgents::getAgentValue($deliveryProposal->agent_id),

        ],
        [
            'displayOnly' => true,
            'type' => DetailView::INPUT_DROPDOWN_LIST,
            'items' => TlCars::getCarArray(),
            'attribute' => 'car_id',
            'value' => TlCars::getCarValue($deliveryProposal->car_id),

        ],

        'driver_name',
        'driver_phone',
        'driver_auto_number',

        'price_invoice:currency',
        'price_invoice_with_vat:currency',


        [
            'displayOnly' => true,
            'attribute' => 'price_expenses_total',
            'format' => 'currency'

        ],

        [
            'displayOnly' => true,
            'attribute' => 'price_expenses_cache',
            'format' => 'currency'

        ],

        [
            'displayOnly' => true,
            'attribute' => 'price_expenses_with_vat',
            'format' => 'currency'

        ],
        [
            'type' => DetailView::INPUT_DROPDOWN_LIST,
            'items' => $deliveryProposal::getPaymentMethodArray(),
            'attribute' => 'cash_no',
            'value' => $deliveryProposal->getPaymentMethodValue(),
        ],
        [
            'displayOnly' => true,
            'type' => DetailView::INPUT_DROPDOWN_LIST,
            'items' => $deliveryProposal::getStatusArray(),
            'attribute' => 'status',
            'value' => $deliveryProposal::getStatusArray($deliveryProposal->status),
        ],
        [
            'type' => DetailView::INPUT_DROPDOWN_LIST,
            'items' => $deliveryProposal::getInvoiceStatusArray(),
            'attribute' => 'status_invoice',
            'value' => $deliveryProposal::getInvoiceStatusArray($deliveryProposal->status_invoice),
        ],
        [
            'type' => DetailView::INPUT_DROPDOWN_LIST,
            'items' => $deliveryProposal::getSourceArray(),
            'attribute' => 'source',
            'value' => $deliveryProposal->getSourceValue(),
        ],
        [
            'type' => DetailView::INPUT_DROPDOWN_LIST,
            'items' => $deliveryProposal::getNoChangePriceArray(),
            'attribute' => 'change_price',
            'value' => $deliveryProposal->getNoChangePriceValue(),
        ],
        [
            'type' => DetailView::INPUT_DROPDOWN_LIST,
            'items' => $deliveryProposal::getNoChangeMcKgNpArray(),
            'attribute' => 'change_mckgnp',
            'value' => $deliveryProposal->getNoChangeMcKgNpValue(),
        ],
        [
            'type' => DetailView::INPUT_TEXTAREA,
            'attribute' => 'comment',
        ],
        [
            'displayOnly' => true,
            'attribute' => 'created_user_id',
            'value' => $deliveryProposal::getUserName($deliveryProposal->created_user_id),
        ],
        [
            'displayOnly' => true,
            'attribute' => 'updated_user_id',
            'value' => $deliveryProposal::getUserName($deliveryProposal->updated_user_id),
        ],
        [
            'displayOnly' => true,
            'attribute' => 'created_at',
            'format' => 'datetime'
        ],
        [
            'displayOnly' => true,
            'attribute' => 'updated_at',
            'format' => 'datetime'

        ],
    ],
]) ?>
</div>