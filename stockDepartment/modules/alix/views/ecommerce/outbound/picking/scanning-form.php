<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 02.02.15
 * Time: 14:58
 */
//use yii\helpers\Html;
//use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;
use yii\helpers\Url;
use yii\helpers\Html;

\stockDepartment\modules\intermode\assets\ScanOutboundFormAsset::register($this);

?>
<h1>Отгрузка Subaru Auto</h1>
<div id="messages-scanning-container">
    <div id="messages-base-line"></div>
    <?= Alert::widget([
        'options' => [
            'id' => 'messages-scanning-list',
            'class' => 'alert-info hidden',
        ],
        'body' => '<span id="messages-scanning-list-body"></span>',
    ]);
    ?>
</div>
<?= Html::tag('span',
    Yii::t('outbound/buttons', 'Print box label'),
    [
        'data-url' => Url::toRoute('print-box-label'),
        'data-validate-url' => Url::toRoute('validate-print-box-label'),
        'class' => 'btn btn-danger',
        'id' => 'outboundform-print-box-label-for-order-bt',
        'style' => 'margin-top:-42px; float:right; font-size: 25px;'
    ]) ?>
<div class="scanning-form">
    <?php $form = ActiveForm::begin([
            'id' => 'outboundform',
            'enableClientValidation' => false,
            'validateOnChange' => false,
            'validateOnSubmit' => false,
        ]
    ); ?>

    <?= $form->field($model, 'employee_barcode')->textInput([
        'class' => 'form-control input-lg',
        'data-url' => Url::toRoute('employee-barcode-handler')
    ]); ?>

    <?= $form->field($model, 'pick_list_barcode'
    )->textInput([
        'class' => 'form-control ext-large-input',
        'data-url' => Url::toRoute('pick-list-barcode-handler')
    ]) ?>

    <?= $form->field($model, 'box_barcode'
    )->textInput([
        'class' => 'form-control ext-large-input',
        'data-url' => Url::toRoute('box-barcode-handler')
    ])->label(Yii::t('outbound/forms', 'Box Barcode')) ?>

    <?= $form->field($model, 'product_barcode'
    )->textInput([
        'class' => 'form-control ext-large-input',
        'data-url' => Url::toRoute('product-barcode-handler')
    ])->label(Yii::t('outbound/forms', 'Product Barcode')) ?>

<!--    --><?php /*echo  $form->field($model, 'fub_barcode'
    )->textInput([
        'class' => 'form-control ext-large-input',
        'data-url' => Url::toRoute('fub-barcode-handler')
    ])->label(Yii::t('outbound/forms', 'Fub Barcode')) */?>

<!--    --><?php //echo $form->field($model, '_on_print_box_label')->hiddenInput(['value'=>0])->label(false)->error(false); ?>

    <?php ActiveForm::end(); ?>

    <div class="row" style="margin: 20px 1px">
        <?= Html::tag('span', Yii::t('outbound/buttons', 'List differences'), ['data-url' => Url::toRoute('print-diff-list'), 'class' => 'btn btn-success', 'id' => 'outboundform-diff-list-bt', 'style' => 'margin-left:10px;']) ?>
        <?= Html::tag('span', Yii::t('outbound/buttons', 'show-picking-list-items'), ['data-url' => Url::toRoute('show-picking-list-items'), 'class' => 'btn btn-success', 'id' => 'outboundform-show-picking-list-items-bt', 'style' => 'margin-left:10px;']) ?>
        <?= Html::tag('span', Yii::t('outbound/buttons', 'Clear Box'), ['data-url' => Url::toRoute('clear-box'), 'class' => 'btn btn-warning pull-right', 'id' => 'outboundform-clear-box-bt', 'style' => 'margin-left:10px;']) ?>
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
    <div id="show-picking-list-items" class="table-responsive"></div>
</div>