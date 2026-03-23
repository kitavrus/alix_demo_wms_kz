<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
//use yii\widgets\ActiveForm;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use common\modules\transportLogistics\components\TLHelper;
use app\modules\transportLogistics\transportLogistics;
use kartik\datecontrol\DateControl;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;


/* @var $this yii\web\View */
/* @var $model common\modules\transportLogistics\models\TlDeliveryRoutes */
/* @var $deliveryProposalModel common\modules\transportLogistics\models\TlDeliveryProposal */
/* @var $form yii\widgets\ActiveForm */
$this->title = Yii::t('transportLogistics/titles','Creating delivery route');
$this->params['breadcrumbs'][] = $this->title;
?>


<h1><?= Html::encode($this->title) ?></h1>

<div class="tl-delivery-proposal-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'route_from',
        [
            'template' => "{label}\n{input-group-begin}{input}{counter}{input-group-end}\n{hint}\n{error}\n",
            'parts' => [
                '{input-group-begin}' => '<div class="input-group">',
                '{input-group-end}' => '</div>',
                '{counter}' => '<div class="input-group-addon" style="background-color: none; border: none; border-radius: none;" ><span class="btn btn-success btn-xs add-route-bt" data-value="' . Url::to(['/tms/default/add-store-route']) . '">Добавить</span></div>',
            ]
        ]
    )->widget(Select2::classname(), [
    'language' => 'ru',
    'data' => TLHelper::getStockPointArray($deliveryProposalModel->client_id),
    'options' => ['placeholder' => Yii::t('transportLogistics/titles', 'Select route from')],
    'pluginOptions' => [
    'allowClear' => true
    ],
    ]);
    ?>

    <?= $form->field($model, 'route_to',
        [
            'template' => "{label}\n{input-group-begin}{input}{counter}{input-group-end}\n{hint}\n{error}\n",
            'parts' => [
                '{input-group-begin}' => '<div class="input-group">',
                '{input-group-end}' => '</div>',
                '{counter}' => '<div class="input-group-addon" style="background-color: none; border: none; border-radius: none;" ><span class="btn btn-success btn-xs add-route-bt" data-value="' . Url::to(['/tms/default/add-store-route']) . '">Добавить</span></div>',
            ]
        ]
    )->widget(Select2::classname(), [
    'language' => 'ru',
    'data' => TLHelper::getStockPointArray($deliveryProposalModel->client_id),
    'options' => ['placeholder' => Yii::t('transportLogistics/titles', 'Select route to')],
    'pluginOptions' => [
    'allowClear' => true
    ],
    ]);
    ?>
    <?= $form->field($model, 'transportation_type')->dropDownList($model->getTransportationTypeArray()); ?>

    <?= $form->field($model, 'shipped_datetime')->widget(DateControl::className(), [
        'type'=>DateControl::FORMAT_DATETIME,

    ]); ?>


<!--    --><?php //if (!$model->isNewRecord) { ?>
    <?= $form->field($model, 'accepted_datetime')->widget(DateControl::className(), [
        'type'=>DateControl::FORMAT_DATETIME,
    ]); ?>

    <?= $form->field($model, 'delivery_date')->widget(DateControl::className(), [
        'type'=>DateControl::FORMAT_DATETIME,

    ]); ?>

<!--    --><?php //} ?>

<!--    --><?//= $form->field($model, 'number_places')->textInput() ?>

<!--    --><?php //if (!$model->isNewRecord) { ?>
<!--        --><?//= $form->field($model, 'number_places_actual')->textInput() ?>
<!--    --><?php //} ?>
<!---->
<!--    --><?//= $form->field($model, 'mc')->textInput(['maxlength' => 26]) ?>
<!---->
<!--    --><?php //if (!$model->isNewRecord) { ?>
<!--        --><?//= $form->field($model, 'mc_actual')->textInput() ?>
<!--    --><?php //} ?>
<!---->
<!--    --><?//= $form->field($model, 'kg')->textInput() ?>
<!---->
<!--    --><?php //if (!$model->isNewRecord) { ?>
<!--        --><?//= $form->field($model, 'kg_actual')->textInput() ?>
<!--    --><?php //} ?>
<!---->

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
<!---->
<!--    --><?php //if (!$model->isNewRecord) { ?>
<!--        --><?//= $form->field($model, 'status_invoice')->dropDownList($model->getInvoiceStatusArray()) ?>
<!--    --><?php //} ?>

    <?= $form->field($model, 'comment')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'tl_delivery_proposal_id',['template'=>'{input}'])->hiddenInput(['value'=>$deliveryProposalModel->id]) ?>

    <?= $form->field($model, 'client_id',['template'=>'{input}'])->hiddenInput(['value'=>$deliveryProposalModel->client_id]) ?>


    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('transportLogistics/buttons', 'Create') : Yii::t('transportLogistics/buttons', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php Modal::begin([
    'header' => '<h2>Добавить новое направление</h2>',
    'id'=>'add-new-rout-modal'
]); ?>
<?= "<div id='modalContent'></div>"; ?>
<?php Modal::end();?>


<?php Modal::begin([
    'header' => '<h2>Добавить или выбрать авто</h2>',
    'id'=>'add-new-route-car-modal'
]); ?>
<?= "<div id='modal-route-car-content'></div>"; ?>
<?php Modal::end();?>
