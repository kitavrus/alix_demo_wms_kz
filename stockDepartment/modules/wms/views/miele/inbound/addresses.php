<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;
use stockDepartment\modules\wms\models\miele\form\InboundChangeAddressForm;

stockDepartment\modules\wms\assets\Miele\ChangeInboundAddressFormAsset::register($this);
/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $af stockDepartment\modules\wms\models\miele\form\InboundChangeAddressForm */
/* @var $newAndInProcess [] */
?>
<h1>Размещение приходов Miele</h1>
<?php $this->title = "Размещение приходов Miele";?>

<div id="messages-container">
    <div id="messages-base-line"></div>
    <?= Alert::widget([
        'options' => [
            'id' => 'messages-list',
            'class' => 'alert-info hidden',
        ],
        'body' => '<span id="messages-list-body"></span>',
    ]);
    ?>
</div>

<div class="stock-accommodation-form">
    <?php $form = ActiveForm::begin([
            'id'=>'inboundchangeaddressform',
            'enableClientValidation'=>false,
            'validateOnChange'=>false,
            'validateOnSubmit'=>false,
        ]
    ); ?>
    <?= $form->field($af, 'order_id')->dropDownList(
        $newAndInProcess,
        [
//            'id' => 'inboundform-order_id',
            'class' => 'form-control input-lg',
//            'data-url' => Url::to('change-order-handler')
        ]
    ); ?>

    <?= $form->field($af, 'type',['labelOptions'=>['id'=>'type-label']])->dropDownList(
        InboundChangeAddressForm::getTypeArray(),
        [
            'prompt'=>'',
        ]
    ); ?>

    <?= $form->field($af, 'from',['labelOptions'=>['id'=>'from-label']])->textInput(); ?>
    <?= $form->field($af, 'to',['labelOptions'=>['id'=>'to-label']])->textInput(); ?>

    <?php ActiveForm::end(); ?>

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