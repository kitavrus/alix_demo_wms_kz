<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;
use yii\helpers\Url;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $orderNumberArray common\modules\inbound\models\InboundOrder */
/* @var $clientsArray common\modules\client\models\Client */
/* @var $outboundForm stockDepartment\modules\outbound\models\OutboundPickListForm */
?>
<?php

//    $this->title = Yii::t('outbound/titles', 'Outbound Orders'); ?>
    <div class="order-process-form">
        <?php $form = ActiveForm::begin([
            'id' => 'outbound-process-form',
            'enableClientValidation' => false,
            'validateOnChange' => false,
            'validateOnSubmit' => false,
        ]
    ); ?>

        <?= $form->field($outboundForm, 'client_id', ['labelOptions' => ['label' => Yii::t('outbound/forms', 'Client ID')]])->dropDownList(
        $clientsArray,
        ['prompt' => '',
            'id' => 'outbound-form-client-id',
            'class' => 'form-control input-lg',
        ]
    ); ?>


        <?= $form->field($outboundForm, 'parent_order_number'
//    ,
//        ['template' => "{label}\n{input-group-begin}{counter}{input}{input-group-end}\n{hint}\n{error}\n",
//            'parts' => [
//                '{label}' => '<label for="outbound-form-order-number">' . Yii::t('outbound/forms', 'Order') . '</label>',
//                '{input-group-begin}' => '<div class="input-group">',
//                '{input-group-end}' => '</div>',
//                '{counter}' => '<div class="input-group-addon" >' . Yii::t('outbound/forms', 'Products') . ': <strong id="count-products-in-order" >0</strong>&nbsp;&nbsp;&nbsp;&nbsp;<span class="in-order">' . Yii::t('outbound/forms', 'In order') . ': </span></div>',
//
//            ]
//        ]
    )->dropDownList(
        [],
//		$orderNumberArray,
        ['prompt' => '',
            'id' => 'outbound-form-parent-order-number',
            'class' => 'form-control input-lg',
        ]
    ); ?>


        <?php ActiveForm::end(); ?>


        <div class="form-group">
            <?= Html::tag('span', Yii::t('outbound/buttons', 'Print Picking List'), ['class' => 'btn btn-primary', 'style' => ' margin-left:10px;', 'id' => 'print-picking-outbound-print-bt','data-url-value'=>Url::to(['/outbound/default/print-pick-list'])]) ?>
        </div>

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
<div id="grid-orders-container">

</div>
<?//= $this->render('_sub-order-grid',[
//    'searchModel' => $searchModel,
//    'dataProvider' => $dataProvider,
//]); ?>


<?php Modal::begin([
    'header' => '<h4 id="outbound-index-header"></h4>',
    'id' => 'outbound-index-modal'
]); ?>
<?= "<div id='outbound-index-errors'></div>"; ?>
<?= "<div id='outbound-index-content'></div>"; ?>
<?php Modal::end(); ?>