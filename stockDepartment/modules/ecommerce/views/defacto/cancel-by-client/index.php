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

\app\modules\ecommerce\assets\defacto\ScanCancelByClientFormAsset::register($this);
?>
<h1>Это отмена только для отгрузок которые отменил клиент Defacto Ecommerce!!!</h1>
<br/>
<?//= Html::tag('span', Yii::t('outbound/buttons', 'Возврат полностью принят'), ['data-url' => Url::toRoute('complete'), 'class' => 'btn btn-danger pull-right', 'id' => 'returnform-complete-bt', 'style' => 'margin-left:10px;']) ?>
<?= Html::tag('span', Yii::t('outbound/buttons', 'Отменяем все отсканированные заказы'), ['data-url' => Url::toRoute('cancel'), 'class' => 'btn btn-warning pull-right', 'id' => 'cancelbyclientform-cancel-bt', 'style' => 'margin-left:10px;']) ?>
<br/>
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
            'id' => 'cancelbyclientform',
            'enableClientValidation' => false,
            'validateOnChange' => false,
            'validateOnSubmit' => false,
        ]
    ); ?>

    <?= $form->field($model, 'cancelKey')->textInput([
        'class' => 'form-control input-lg',
        'data-url' => Url::toRoute('cancel-key')
    ]); ?>

    <?= $form->field($model, 'outboundOrderNumber')->textInput([
        'class' => 'form-control input-lg',
        'data-url' => Url::toRoute('order-number')
    ]); ?>

    <?= $form->field($model, 'boxAddress')->textInput([
        'class' => 'form-control input-lg',
        'data-url' => Url::toRoute('set-box-address')
    ]); ?>

    <?php ActiveForm::end(); ?>

    <div class="row" style="margin: 20px 1px">
        <?= Html::tag('span', Yii::t('outbound/buttons', 'Что внутри заказа'), ['data-url' => Url::toRoute('show-order-items'), 'class' => 'btn btn-success pull-left', 'id' => 'cancelbyclientform-show-order-items-bt', 'style' => 'margin-left:10px;']) ?>
        <?= Html::tag('span', Yii::t('outbound/buttons', 'Что внутри всех заказов'), ['data-url' => Url::toRoute('show-all-order-items'), 'class' => 'btn btn-success pull-left', 'id' => 'cancelbyclientform-show-all-order-items-bt', 'style' => 'margin-left:10px;']) ?>
        <?= Html::tag('span', Yii::t('outbound/buttons', 'Очистить короб'), ['data-url' => Url::toRoute('empty-box'), 'class' => 'btn btn-danger pull-right', 'id' => 'cancelbyclientform-empty-box-bt', 'style' => 'margin-left:10px;']) ?>
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
    <div id="show-items" class="table-responsive"></div>
</div>