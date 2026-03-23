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
//use yii\bootstrap\Modal;
use yii\helpers\Html;

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

<div class="scanning-form">
<?php $form = ActiveForm::begin([
        'id' => 'scanning-colins-form',
        'enableClientValidation' => false,
        'validateOnChange' => false,
        'validateOnSubmit' => false,
    ]
); ?>

<?= $form->field($model, 'employee_barcode')->textInput([
    'class' => 'form-control input-lg',
    'data' => ['url'=>Url::toRoute('/warehouseDistribution/colins/outbound/employee-barcode-scanning-handler')]
]); ?>

<?= $form->field($model, 'order_shop',
    ['template' => "{alert}\n{label}\n{input-group-begin}{input}{button-right}{input-group-end}\n{hint}\n{error}\n",
        'parts' => [
            '{alert}' => '<div class="-" id="alert-picking-list"></div>',
            '{input-group-begin}' => '<div class="input-group">',
            '{input-group-end}' => '</div>',
            '{button-right}' => '<div class="input-group-addon"><span style="font-size: 30px;" id="order-exp-accept">0/0</span><span style="font-size: 30px;"> | </span><span style="font-size: 30px;" id="order-shop-name">-</span></div> '
        ]
    ]
)->dropDownList($orderShops,[
    'prompt'=>'',
    'class' => 'form-control ext-large-input',
    'data' => ['url'=>Url::toRoute('/warehouseDistribution/colins/outbound/order-shop-scanning-handler')]
])
?>

<?= $form->field($model, 'box_barcode', [
    'template' => "{label}\n{input-group-begin}{counter}{input}{button-right}{input-group-end}\n{hint}\n{error}\n",
    'parts' => [
        '{label}' => '<label for="scanningcolinsform-box_barcode">' . Yii::t('outbound/forms', 'Box Barcode') . '</label>',
        '{input-group-begin}' => '<div class="input-group">',
        '{input-group-end}' => '</div>',
        '{counter}' => '<div class="input-group-addon" style="font-size: 30px;">' . Yii::t('outbound/forms', 'Products') . ': <strong id="count-product-in-box" >0</strong>&nbsp;&nbsp;&nbsp;&nbsp;<span class="in-box">' . Yii::t('outbound/forms', 'In box') . ': </span></div>',
        '{button-right}' => '<div class="input-group-addon" style="background-color: none; border: none; border-radius: none;" ><span class="btn btn-success btn-xs" data-url-value="' . Url::toRoute(['clear-box']) . '" id="clear-box-scanning-colins-outbound-bt">' . Yii::t('outbound/buttons', 'Clear Box') . '</span></div>'
    ]
])->textInput([
    'class' => 'form-control ext-large-input',
    'data' => ['url'=>Url::toRoute('/warehouseDistribution/colins/outbound/box-barcode-scanning-handler')]
])->label(Yii::t('outbound/forms', 'Box Barcode')) ?>


<?= $form->field($model, 'product_barcode',
['labelOptions' => ['label' => Yii::t('outbound/forms', 'Product Barcode')],
            'template' => "{label}\n{input-group-begin}{input}{button-right}{input-group-end}\n{hint}\n{error}\n",
            'parts' => [
    '{label}' => '<label for="scanningcolinsform-box_barcode">' . Yii::t('outbound/forms', 'Box Barcode') . '</label>',
    '{input-group-begin}' => '<div class="input-group">',
    '{input-group-end}' => '</div>',
    '{button-right}' => '<div class="input-group-addon" style="background-color: none; border: none; border-radius: none;" ><span class="btn btn-success btn-xs" data-url-value="' . Url::toRoute(['clear-product-in-box-by-one']) . '" id="clear-product-in-box-by-one-scanning-colins-outbound-bt">' . Yii::t('outbound/buttons', 'Clear product in box') . '</span></div>'
]
		]
)->textInput([
    'class' => 'form-control ext-large-input',
    'data' => ['url'=>Url::toRoute('/warehouseDistribution/colins/outbound/product-barcode-scanning-handler')]
])->label(Yii::t('outbound/forms', 'Product Barcode')) ?>

<?php ActiveForm::end(); ?>
<div class="row" style="margin: 20px 1px">

    <?= Html::tag('span', Yii::t('outbound/buttons', 'List differences'), ['data-url' => Url::toRoute('printing-differences-list'), 'class' => 'btn btn-success', 'id' => 'scanning-colins-form-differences-list-bt', 'style' => 'margin-left:10px;']) ?>
    <?= Html::tag('span', Yii::t('outbound/buttons', 'Print box label'), ['data-url' => Url::toRoute('printing-box-label'), 'class' => 'btn btn-success', 'id' => 'scanning-colins-form-print-box-label-bt', 'style' => 'margin-right:10px; float:right;']) ?>

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
<div id="outbound-items" class="table-responsive">
    <table class="table">
        <tr>
            <th><?= Yii::t('outbound/forms', 'Product Barcode'); ?></th>
            <th><?= Yii::t('outbound/forms', 'Product Model'); ?></th>
            <th><?= Yii::t('outbound/forms', 'Expected Qty'); ?></th>
            <th><?= Yii::t('outbound/forms', 'Accepted Qty'); ?></th>
        </tr>
        <tbody id="outbound-item-body"></tbody>
    </table>
</div>