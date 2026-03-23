<?php
use yii\helpers\Html;
//use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;
use yii\helpers\Url;
//use yii\bootstrap\Modal;

\stockDepartment\modules\wms\assets\subaruAuto\BeginEndPickListOutboundAsset::register($this);
?>
    <div id="messages-container">
        <div id="messages-base-line"></div>
        <?= Alert::widget([
            'closeButton'=>false,
            'options' => [
                'id' => 'messages-list',
                'class' => 'alert-info hidden',
                'data-url' => Url::toRoute('begin-end-picking-handler')
            ],
            'body' => '<span id="messages-list-body"></span>',
        ]);
        ?>
    </div>
    <div class="begin-end-pick-list-form">
<?php $form = ActiveForm::begin([
        'id' => 'begin-end-pick-list-form',
        'enableClientValidation' => false,
        'validateOnChange' => false,
        'validateOnSubmit' => false,
    ]
); ?>

<?= $form->field($model, 'picking_list_barcode')->textInput([
    'data-url' => Url::toRoute('begin-end-picking-handler')
])->label(Yii::t('outbound/forms', 'Picking list barcode')); // ,['template'=>'{input}'] ?>
<?= $form->field($model, 'employee_barcode')->textInput([
    'data-url' => Url::toRoute('begin-end-picking-handler')
])->label(Yii::t('outbound/forms', 'Employee barcode')); // , ['template'=>'{input}'] ?>
<?php ActiveForm::end(); ?>