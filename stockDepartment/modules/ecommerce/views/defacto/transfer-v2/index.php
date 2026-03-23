<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 12.12.2019
 * Time: 10:01
 */

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;
use yii\helpers\Url;
use yii\helpers\Html;

\app\modules\ecommerce\assets\defacto\TransferFormV2Asset::register($this);
?>
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
<h1 style=" float:left; font-size: 25px;">
    Трансфер Defacto Ecommerce
</h1>
<br/>
<?= Html::tag('span', Yii::t('outbound/buttons', 'Накладная собрана'), ['data-url' => Url::toRoute('complete'), 'class' => 'btn btn-danger pull-right', 'id' => 'transferformv2-complete-bt', 'style' => 'margin-left:10px;']) ?>
<br/>
<br/>
<?php $form = ActiveForm::begin([
        'id' => 'transferformv2',
        'enableClientValidation' => false,
        'validateOnChange' => false,
        'validateOnSubmit' => false,
    ]
); ?>

<?= $form->field($model, 'pickingListBarcode',
    ['template' => "{label}\n{input-group-begin}{button-right}{input}{input-group-end}\n{hint}\n{error}\n",
        'parts' => [
            '{input-group-begin}' => '<div class="input-group">',
            '{input-group-end}' => '</div>',
            '{button-right}' => '<div class="input-group-addon" style="font-size: 62px;" id="order-qty">0/0</div>'
        ]
    ]
)->textInput([
    'class' => 'form-control ext-large-input',
    'data-url' => Url::toRoute('picking-list-barcode')
])
?>

<?= $form->field($model, 'lcBarcode',
    ['template' => "{label}\n{input-group-begin}{button-right}{input}{input-group-end}\n{hint}\n{error}\n",
        'parts' => [
            '{input-group-begin}' => '<div class="input-group">',
            '{input-group-end}' => '</div>',
            '{button-right}' => '<div class="input-group-addon" style="font-size: 62px;" id="lc-box-barcode-qty">0</div>'
        ]
    ])->textInput([
        'class' => 'form-control ext-large-input',
        'data-url' => Url::toRoute('lc-barcode')
    ])?>

<?= $form->field($model, 'productBarcode',
    ['template' => "{label}\n{input-group-begin}{button-right}{input}{input-group-end}\n{hint}\n{error}\n",
        'parts' => [
            '{input-group-begin}' => '<div class="input-group">',
            '{input-group-end}' => '</div>',
            '{button-right}' => '<div class="input-group-addon" style="font-size: 62px;" id="product-qty">0</div>'
        ]
    ]
)->textInput([
    'class' => 'form-control ext-large-input',
    'data-url' => Url::toRoute('product-barcode'),
    'data-move-all-url' => Url::toRoute('move-all-products')
])
?>

<?php ActiveForm::end(); ?>

<div class="row" style="margin: 20px 1px">
    <?= Html::tag('span', Yii::t('outbound/buttons', 'Содержимое LC короба'), ['data-url' => Url::toRoute('show-lc-box-items'), 'class' => 'btn btn-success', 'id' => 'transferformv2-show-lc-box-items-bt', 'style' => 'margin-left:10px;']) ?>
    <?= Html::tag('span', Yii::t('outbound/buttons', 'Отсканированные'), ['data-url' => Url::toRoute('show-scanned-items'), 'class' => 'btn btn-warning', 'id' => 'transferformv2-show-scanned-items-bt', 'style' => 'margin-left:10px;']) ?>
    <?= Html::tag('span', Yii::t('outbound/buttons', 'Содержимое заказа'), ['data-url' => Url::toRoute('show-order-items'), 'class' => 'btn btn-warning', 'id' => 'transferformv2-show-order-items-bt', 'style' => 'margin-left:10px;']) ?>
    <?= Html::tag('span', Yii::t('outbound/buttons', 'Clear Box'), ['data-url' => Url::toRoute('empty-box'), 'class' => 'btn btn-danger pull-right', 'id' => 'transferformv2-empty-box-bt', 'style' => 'margin-left:10px;']) ?>
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
