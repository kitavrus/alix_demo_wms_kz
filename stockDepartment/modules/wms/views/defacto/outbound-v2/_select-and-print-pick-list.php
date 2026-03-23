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
        'options' => [
            'data-printType' => \Yii::$app->params['printType']
        ]
        ]
    ); ?>


        <?= $form->field($outboundForm, 'parent_order_number'
    )->dropDownList(
        [],
        ['prompt' => '',
            'id' => 'outbound-form-parent-order-number',
            'class' => 'form-control input-lg',
        ]
    ); ?>


        <?php ActiveForm::end(); ?>


        <div class="form-group">
            <?= Html::tag('span', Yii::t('outbound/buttons', 'Print Picking List'), [
                'class' => 'btn btn-primary',
                'style' => ' margin-left:10px;',
                'id' => 'print-picking-outbound-print-bt',
                'data-url-value'=>Url::toRoute('print-pick-list')
            ]) ?>
			<?= Html::tag('span', Yii::t('outbound/buttons', 'Print Picking List EXL'), [
                'class' => 'btn btn-warning',
                'style' => ' margin-left:10px;',
                'id' => 'print-picking-outbound-print-bt',
                'data-url-value'=>Url::toRoute('print-pick-list-exl')
            ]) ?>
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


<?php Modal::begin([
    'header' => '<h4 id="outbound-index-header"></h4>',
    'id' => 'outbound-index-modal'
]); ?>
<?= "<div id='outbound-index-errors'></div>"; ?>
<?= "<div id='outbound-index-content'></div>"; ?>
<?php Modal::end(); ?>