<?php
/**
 * Created by PhpStorm.
 * User: Kitavrus
 * Date: 17.04.15
 * Time: 10:45
 */
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;
use yii\helpers\Url;
?>

<?php $this->title = Yii::t('inbound/titles', 'Generate Cross Dock Picking List'); ?>

<div class="cross-dock-process-form">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php $form = ActiveForm::begin([
            'id' => 'cross-dock-print-box-barcode-form',
            'enableClientValidation' => false,
            'validateOnChange' => false,
            'validateOnSubmit' => false,
            'options' => [
                'data-printType' => \Yii::$app->params['printType']
            ]
        ]
    ); ?>

<!--    --><?php /*echo $form->field($formModel, 'client_id', ['labelOptions' => ['label' => false]])->dropDownList(
        $clientsArray,
        ['prompt' => '',
            'id' => 'cross-dock-form-client-id',
            'class' => 'form-control input-lg hidden',
        ]
    ); */?>

    <?= $form->field($formModel, 'order_number'
    )->dropDownList($orderListData,
        [
            'prompt' => '',
            'id' => 'print-box-barcode-cross-dock-order-number',
            'data-url' => Url::toRoute('get-cross-dock-order-list'),
            'class' => 'form-control input-lg',
        ]
    )->label(Yii::t('inbound/forms', 'Party number')); ?>

    <?php ActiveForm::end(); ?>

<!--    <div class="form-group">
        <?php /*echo  Html::tag('span', Yii::t('inbound/buttons', 'Print'), [
            'class' => 'btn btn-danger pull-right',
            'style' => ' margin-left:10px;',
            'data-url' => Url::toRoute('print-cross-dock-list'),
            'id' => 'cross-dock-print-bt'
        ]) */?>
    </div>-->

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
    <div id="grid-orders-container"></div>
</div>