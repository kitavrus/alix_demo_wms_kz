<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 29.10.2019
 * Time: 15:57
 */

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;
use yii\helpers\Url;
use yii\helpers\Html;

\app\modules\ecommerce\assets\defacto\ScanCancelFormAsset::register($this);

?>
<h1>Отмена Отгрузок Defacto Ecommerce</h1>
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
            'id' => 'cancelform',
            'enableClientValidation' => false,
            'validateOnChange' => false,
            'validateOnSubmit' => false,
        ]
    ); ?>

    <?= $form->field($model, 'outboundOrderNumber')->textInput([
        'class' => 'form-control input-lg',
        'data-url' => Url::toRoute('order-number')
    ]); ?>
    <?php $model->cancelReason = \common\ecommerce\constants\OutboundCancelStatus::UNABLE_TO_FULFIL;  ?>
    <?php echo $form->field($model, 'cancelReason')->dropDownList(\common\ecommerce\constants\OutboundCancelStatus::getForCancelAll())->label(Yii::t('outbound/forms', 'Причина отмены')) ?>
<!--    --><?php //echo $form->field($model, 'cancelReason')->dropDownList(\common\ecommerce\constants\OutboundCancelStatus::getAll())->label(Yii::t('outbound/forms', 'Причина отмены')) ?>

    <?php ActiveForm::end(); ?>
    <div class="row" style="margin: 20px 1px">
        <?= Html::tag('span', Yii::t('outbound/buttons', 'Отменить'), ['data-url' => Url::toRoute('cancel'), 'class' => 'btn btn-warning pull-right', 'id' => 'cancelform-cancel-bt', 'style' => 'margin-left:10px;']) ?>
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