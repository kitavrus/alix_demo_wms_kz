<?php
use stockDepartment\modules\wms\assets\DeFactoAsset;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $inboundForm stockDepartment\modules\wms\models\miele\form\InboundForm */
/* @var $newAndInProcess common\modules\inbound\models\InboundOrder */

stockDepartment\modules\wms\assets\Miele\ScanFormInboundAsset::register($this);
?>

<?php $this->title = Yii::t('return/titles', 'Miele inbound'); ?>
<div class="movement-form">
    <?php $form = ActiveForm::begin([
            'id' => 'inboundform',
            'enableClientValidation' => false,
            'validateOnChange' => false,
            'validateOnSubmit' => false,
        ]
    ); ?>

    <?= $form->field($inboundForm, 'order_id')->dropDownList(
        $newAndInProcess,
        ['prompt' => '',
//            'id' => 'inboundform-order_id',
            'class' => 'form-control input-lg',
            'data-url' => Url::to('change-order-handler')
        ]
    ); ?>

    <?= $form->field($inboundForm, 'our_box_barcode')->textInput(
        [
//            'id' => 'inboundform-our_box_barcode',
            'class' => 'form-control input-lg',
            'data-url' => Url::to('our-box-barcode-handler')
        ]
    ); ?>

    <?= $form->field($inboundForm, 'product_barcode')->textInput([
//        'id' => 'inboundform-product_barcode',
        'class' => 'form-control input-lg',
        'data-url' => Url::to('product-barcode-handler')
    ]); ?>

    <?= $form->field($inboundForm, 'fab_barcode')->textInput([
//        'id' => 'inboundform-fab_barcode',
        'class' => 'form-control input-lg',
        'data-url' => Url::to('fab-barcode-handler')
    ]); ?>

    <div class="form-group">
        <?= Html::tag('div', Yii::t('inbound/buttons', 'очистить короб'), [
            'class' => 'btn btn-danger',
            'data-url' => Url::toRoute('clean-our-box-handler'),
            'style' => 'with:100%',
            'id' => 'inboundform-clean-our-box-bt'
        ]) ?>
        <?= Html::tag('div', Yii::t('inbound/buttons', 'Расхождения'), [
            'class' => 'btn btn-success pull-right',
            'data-url' => Url::toRoute('print-diff-handler'),
            'style' => 'with:100%',
            'id' => 'inboundform-print-diff-bt'
        ]) ?>
    </div>
    <?php ActiveForm::end(); ?>
    <div id="error-container">
        <div id="error-base-line"></div>
        <?= \yii\bootstrap\Alert::widget([
            'options' => [
                'id' => 'error-list',
                'class' => 'alert-danger hidden',
            ],
            'body' => '',
        ]);
        ?>
    </div>
</div>