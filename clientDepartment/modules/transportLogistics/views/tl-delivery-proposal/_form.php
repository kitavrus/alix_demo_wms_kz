<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
//use yii\widgets\ActiveForm;
//use dosamigos\datepicker\DatePicker;
use yii\bootstrap\Modal;
use yii\helpers\Url;
//use frontend\modules\client\models\Client;
use common\modules\transportLogistics\components\TLHelper;
use app\modules\transportLogistics\transportLogistics;
use kartik\datecontrol\DateControl;
use common\modules\client\models\Client;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;

//use frontend\modules\transportLogistics\models\TlAgents;
//use frontend\modules\transportLogistics\models\TlCars;

/* @var $this yii\web\View */
/* @var $model common\modules\transportLogistics\models\TlDeliveryProposal */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tl-delivery-proposal-form">

    <?php $form = ActiveForm::begin(); ?>

<!--    --><?//= $form->field($model, 'client_id')->dropDownList(ArrayHelper::map(Client::findAll(['status' => Client::STATUS_ACTIVE]), 'id', 'username')); ?>

    <?=
    $form->field($model, 'route_from'
//        ,
//        [
//            'template' => "{label}\n{input-group-begin}{input}{counter}{input-group-end}\n{hint}\n{error}\n",
//            'parts' => [
//                '{input-group-begin}' => '<div class="input-group">',
//                '{input-group-end}' => '</div>',
//                '{counter}' => '<div class="input-group-addon" ><span class="btn btn-success btn-xs add-route-bt" data-value="' . Url::to(['/transportLogistics/tl-delivery-proposal/add-store-route']) . '">Добавить</span></div>',
//            ]
//        ]
    )
//        ->dropDownList(
//        TLHelper::getStoreArrayByClientID(
//            Client::findOne(['user_id'=>Yii::$app->user->id])->id,true)
//    )

    ->widget(Select2::classname(), [
    'language' => 'ru',
    'data' => $storeArray,
    'options' => ['placeholder' => Yii::t('transportLogistics/titles', 'Select route from')],
    'pluginOptions' => [
    'allowClear' => true
    ],
    ]);
    ?>

    <?=
    $form->field($model, 'route_to'
//        ,
//        [
//            'template' => "{label}\n{input-group-begin}{input}{counter}{input-group-end}\n{hint}\n{error}\n",
//            'parts' => [
//                '{input-group-begin}' => '<div class="input-group">',
//                '{input-group-end}' => '</div>',
//                '{counter}' => '<div class="input-group-addon" ><span class="btn btn-success btn-xs add-route-bt" data-value="' . Url::to(['/transportLogistics/tl-delivery-proposal/add-store-route']) . '">Добавить</span></div>',
//            ]
//        ]

    )->widget(Select2::classname(), [
        'language' => 'ru',
        'data' => $storeArray,
        'options' => ['placeholder' => Yii::t('transportLogistics/titles', 'Select route to')],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]);

//        ->dropDownList(
//        TLHelper::getStoreArrayByClientID(
//            Client::findOne(['user_id'=>Yii::$app->user->id])->id,true)
//    ) ?>

<!--    --><?//=
//    $form->field($model, 'shipped_datetime')->widget(DateControl::className(), [
//        'type'=>DateControl::FORMAT_DATETIME,
//
//    ]); ?>

    <?=
    $form->field($model, 'expected_delivery_date')->widget(DateControl::className(), [
        'type'=>DateControl::FORMAT_DATETIME,

    ]); ?>

    <?php if(in_array($model->client_id,[1])) { ?>
        <?=
        $form->field($model, 'delivery_date')->widget(DateControl::className(), [
            'type'=>DateControl::FORMAT_DATETIME,

        ]); ?>
    <?php } ?>

<!--    --><?//= $form->field($model, 'mc')->textInput(['maxlength' => 26]) ?>

<!--    --><?php //if (!$model->isNewRecord) { ?>
<!--        --><?//= $form->field($model, 'mc_actual')->textInput() ?>
<!--    --><?php //} ?>

<!--    --><?//= $form->field($model, 'kg')->textInput() ?>

<!--    --><?php //if (!$model->isNewRecord) { ?>
<!--        --><?//= $form->field($model, 'kg_actual')->textInput() ?>
<!--    --><?php //} ?>

    <?= $form->field($model, 'number_places')->textInput() ?>

<!--    --><?php //if (!$model->isNewRecord) { ?>
<!--        --><?//= $form->field($model, 'number_places_actual')->textInput() ?>
<!--    --><?php //} ?>


<!--    --><?//= $form->field($model, 'cash_no')->dropDownList($model->getPaymentMethodArray()) ?>

<!--    --><?php //if (!$model->isNewRecord) { ?>
<!--        --><?//= $form->field($model, 'price_invoice')->textInput() ?>
<!--    --><?php //} ?>
<!---->
<!--    --><?php //if (!$model->isNewRecord) { ?>
<!--        --><?//= $form->field($model, 'price_invoice_with_vat')->textInput(['maxlength' => 26]) ?>
<!--    --><?php //} ?>

<!--    --><?php //if (!$model->isNewRecord) { ?>
<!--        --><?//= $form->field($model, 'status')->dropDownList($model->getStatusArray()) ?>
<!--    --><?php //} ?>

<!--    --><?php //if (!$model->isNewRecord) { ?>
<!--        --><?//= $form->field($model, 'status_invoice')->dropDownList($model->getInvoiceStatusArray()) ?>
<!--    --><?php //} ?>

    <?= $form->field($model, 'comment')->textarea(['rows' => 6]) ?>

<!--    --><?//= Html::activeHiddenInput($model,'client_id',['value'=>Yii::$app->user->id]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('transportLogistics/buttons', 'Create') : Yii::t('transportLogistics/buttons', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php Modal::begin([
    'header' => '<h2>Добавить новое направление</h2>',
    'id' => 'add-new-rout-modal'
]); ?>
<?= "<div id='modalContent'></div>"; ?>
<?php Modal::end(); ?>
