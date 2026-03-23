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

\stockDepartment\modules\wms\assets\Miele\ScanFormMovementAsset::register($this);

?>
<h1>ПЕРЕМЕЩЕНИЕ MIELE</h1>
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
<div class="scanning-form">
    <?php $form = ActiveForm::begin([
            'id' => 'movementform',
            'enableClientValidation' => false,
            'validateOnChange' => false,
            'validateOnSubmit' => false,
        ]
    ); ?>

    <?= $form->field($model, 'employee_barcode')->textInput([
        'class' => 'form-control input-lg',
        'data-url' => Url::toRoute('employee-handler')
    ]); ?>

    <?= $form->field($model, 'pick_list_barcode'
    )->textInput([
        'class' => 'form-control ext-large-input',
        'data-url' => Url::toRoute('pick-list-handler')
    ]) ?>
    <?= $form->field($model, 'product_barcode'
    )->textInput([
        'class' => 'form-control ext-large-input',
        'data-url' => Url::toRoute('product-handler')
    ])->label(Yii::t('outbound/forms', 'Product Barcode')) ?>

    <?= $form->field($model, 'fub_barcode'
    )->textInput([
        'class' => 'form-control ext-large-input',
        'data-url' => Url::toRoute('fub-handler')
    ])->label(Yii::t('outbound/forms', 'FAB Barcode')) ?>

    <?= $form->field($model, 'to_box'
    )->textInput([
        'class' => 'form-control ext-large-input',
        'data-url' => Url::toRoute('box-handler')
    ])->label(Yii::t('outbound/forms', 'Box Barcode')) ?>


    <?= $form->field($model, 'to_address'
    )->textInput([
        'class' => 'form-control ext-large-input',
        'data-url' => Url::toRoute('address-handler')
    ])->label(Yii::t('outbound/forms', 'Полка')) ?>


    <?php ActiveForm::end(); ?>

    <div class="row" style="margin: 20px 1px">
        <?= Html::tag('span', Yii::t('outbound/buttons', 'List differences'), ['data-url' => Url::toRoute('print-diff-list'), 'class' => 'btn btn-success', 'id' => 'movementform-diff-list-bt', 'style' => 'margin-left:10px;']) ?>
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
</div>