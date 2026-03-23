<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
//use dosamigos\datepicker\DatePicker;
//use yii\bootstrap\Modal;
use yii\helpers\Url;
use common\modules\client\models\Client;
use common\modules\transportLogistics\components\TLHelper;
use app\modules\transportLogistics\transportLogistics;
use kartik\datecontrol\DateControl;


/* @var $this yii\web\View */
/* @var $model common\modules\transportLogistics\models\TlDeliveryProposalOrders */
/* @var $deliveryProposalModel common\modules\transportLogistics\models\TlDeliveryProposal */
/* @var $client common\modules\client\models\Client */
/* @var $form yii\widgets\ActiveForm */
?>
<h1>
    <?= Html::encode(Yii::t('transportLogistics/app','Заявка на доставку')) ?>
    <?= Html::a(Yii::t('transportLogistics/forms', 'Update'), ['update', 'id' => $deliveryProposalModel->id], ['class' => 'btn btn-primary','style' => 'float:right; ',]) ?>
</h1>

<?= DetailView::widget([
    'model' => $deliveryProposalModel,
    'attributes' => [
//        'id',
        'client_id',
        'route_from',
        'route_to',
        'delivery_date',
        'mc',
        'mc_actual',
        'kg',
        'kg_actual',
        'number_places',
        'number_places_actual',
        'cash_no',
        'price_invoice',
        'price_invoice_with_vat',
        'status',
        'status_invoice',
        'comment:ntext',
//        'created_user_id',
//        'updated_user_id',
//        'created_at',
//        'updated_at',
    ],
]) ?>

<h1><?= Html::encode(Yii::t('transportLogistics/app','Строим маршрут для доставки')) ?></h1>

<div class="tl-delivery-proposal-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'client_id')->dropDownList(ArrayHelper::map(Client::findAll(['status' => Client::STATUS_ACTIVE]), 'id', 'username')); ?>

    <?= $form->field($model, 'route_from',
        [
            'template' => "{label}\n{input-group-begin}{input}{counter}{input-group-end}\n{hint}\n{error}\n",
            'parts' => [
                '{input-group-begin}' => '<div class="input-group">',
                '{input-group-end}' => '</div>',
                '{counter}' => '<div class="input-group-addon" ><span class="btn btn-success btn-xs add-route-bt" data-value="' . Url::to(['/tms/order/add-new-route']) . '">Добавить</span></div>',
            ]
        ]
    )->dropDownList(TLHelper::getStockPointArray()) ?>

    <?= $form->field($model, 'route_to',
        [
            'template' => "{label}\n{input-group-begin}{input}{counter}{input-group-end}\n{hint}\n{error}\n",
            'parts' => [
                '{input-group-begin}' => '<div class="input-group">',
                '{input-group-end}' => '</div>',
                '{counter}' => '<div class="input-group-addon" ><span class="btn btn-success btn-xs add-route-bt" data-value="' . Url::to(['/tms/order/add-new-route']) . '">Добавить</span></div>',
            ]
        ]

    )->dropDownList(TLHelper::getStockPointArray()) ?>

    <?=
    $form->field($model, 'delivery_date')->widget(DateControl::className(), [
            'type'=>DateControl::FORMAT_DATETIME,

        ]); ?>

    <?= $form->field($model, 'mc')->textInput(['maxlength' => 26]) ?>

    <?php if (!$model->isNewRecord) { ?>
        <?= $form->field($model, 'mc_actual')->textInput() ?>
    <?php } ?>

    <?= $form->field($model, 'kg')->textInput() ?>

    <?php if (!$model->isNewRecord) { ?>
        <?= $form->field($model, 'kg_actual')->textInput() ?>
    <?php } ?>

    <?= $form->field($model, 'number_places')->textInput() ?>

    <?php if (!$model->isNewRecord) { ?>
        <?= $form->field($model, 'number_places_actual')->textInput() ?>
    <?php } ?>


    <?= $form->field($model, 'route_from',
        [
            'template' => "{label}\n{input-group-begin}{input}{counter}{input-group-end}\n{hint}\n{error}\n",
            'parts' => [
                '{input-group-begin}' => '<div class="input-group">',
                '{input-group-end}' => '</div>',
                '{counter}' => '<div class="input-group-addon" ><span class="btn btn-success btn-xs add-route-bt" data-value="' . Url::to(['/tms/order/add-new-route']) . '">Добавить</span></div>',
            ]
        ]
    )->dropDownList(TLHelper::getStockPointArray()) ?>


    <div class="input-group-addon" ><span class="btn btn-success btn-xs add-car-route-bt" data-value="<?= Url::to(['/tms/default/add-new-car-route']) ?> ">Добавить</span></div>

    <?= $form->field($model, 'cash_no')->dropDownList(['1' => 'Наличный', '2' => 'Без нал']) ?>

    <?php if (!$model->isNewRecord) { ?>
        <?= $form->field($model, 'price_invoice')->textInput() ?>
    <?php } ?>

    <?php if (!$model->isNewRecord) { ?>
        <?= $form->field($model, 'price_invoice_with_vat')->textInput(['maxlength' => 26]) ?>
    <?php } ?>

    <?php if (!$model->isNewRecord) { ?>
        <?= $form->field($model, 'status')->dropDownList($model->getStatusArray()) ?>
    <?php } ?>

    <?php if (!$model->isNewRecord) { ?>
        <?= $form->field($model, 'status_invoice')->dropDownList(['1' => 'не оплачен', '2' => 'оплачен']) ?>
    <?php } ?>

    <?= $form->field($model, 'comment')->textarea(['rows' => 6]) ?>


    <!--    --><?//= $form->field($model, 'route_to',
    //        [
    //            'template' => "{label}\n{input-group-begin}{input}{counter}{input-group-end}\n{hint}\n{error}\n",
    //            'parts' => [
    //                '{input-group-begin}' => '<div class="input-group">',
    //                '{input-group-end}' => '</div>',
    //                '{counter}' => '<div class="input-group-addon" ><span class="btn btn-success btn-xs add-route-bt" data-value="' . Url::to(['/transportLogistics/order/add-new-route']) . '">Добавить</span></div>',
    //            ]
    //        ]
    //
    //    )->dropDownList(TLHelper::getStoreArrayByClientID()) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('transportLogistics/forms', 'Create') : Yii::t('transportLogistics/forms', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php //Modal::begin([
//    'header' => '<h2>Добавить или выбрать авто</h2>',
//    'id'=>'add-new-car-route-modal'
//]); ?>
<?//= "<div id='modalContent'></div>"; ?>
<?php //Modal::end();?>
