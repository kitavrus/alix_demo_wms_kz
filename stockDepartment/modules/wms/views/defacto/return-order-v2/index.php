<?php
/**
 * Created by PhpStorm.
 * User: Igor
 * Date: 08.01.15
 * Time: 7:02
 */

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;
use yii\helpers\Url;
use stockDepartment\modules\wms\assets\DeFactoAsset;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $orderNumberArray common\modules\inbound\models\ReturnOrder */
/* @var $clientsArray common\modules\client\models\Client */
/* @var $inboundForm stockDepartment\modules\inbound\models\ReturnForm */

DeFactoAsset::register($this);
?>

<?php $this->title = Yii::t('return/titles', 'Return Orders'); ?>
<div class="order-process-form">
    <?php $form = ActiveForm::begin([
            'id' => 'return-process-form',
            'enableClientValidation' => false,
            'validateOnChange' => false,
            'validateOnSubmit' => false,
            'options' => [
                'data-printType' => \Yii::$app->params['printType']
            ]
        ]
    ); ?>

    <?= $form->field($returnForm, 'client_id', ['labelOptions' => ['label' => false]])->hiddenInput(['id'=>'return-form-client-id'])?>

    <?= $form->field($returnForm, 'party_number'
        ,
        ['template' => "{label}\n{input-group-begin}{counter}{input}{input-group-end}\n{hint}\n{error}\n",
            'parts' => [
//                '{label}' => '<label for="return-form-party-number">' . Yii::t('return/forms', 'Order') . '</label>',
                '{input-group-begin}' => '<div class="input-group">',
                '{input-group-end}' => '</div>',
                '{counter}' => '<div class="input-group-addon" >' . Yii::t('return/titles', 'Коробов') . ': <strong id="count-boxes-in-party" >0</strong>&nbsp;/&nbsp;<strong id="count-boxes-in-party-scanned">0</strong>&nbsp;&nbsp;&nbsp;&nbsp;<span class="in-order-party">' . Yii::t('return/titles', 'в партии') . ': </span></div>',

            ]
        ]
    )->dropDownList(
        $ordersArray,
        ['prompt' => '',
            'id' => 'return-form-party-number',
            'class' => 'form-control input-lg',
            'data-url' => '/wms/defacto/return-order-v2/show-boxes-for-order'
        ]
    ); ?>

    <?= $form->field($returnForm, 'box_barcode', [
        'template' => "{label}\n{input-group-begin}{input}{button-right}{input-group-end}\n{hint}\n{error}\n",
        'parts' => [
            '{label}' => '<label for="returnform-box_barcode">' . Yii::t('return/forms', 'Box Barcode') . '</label>',
            '{input-group-begin}' => '<div class="input-group">',
            '{input-group-end}' => '</div>',
            '{counter}' => '<div class="input-group-addon" >' .''. '</div>',
            '{button-right}' => '<div class="input-group-addon" style="background-color: none; border: none; border-radius: none;" >'.''.'</div>'
        ]
    ])->textInput(
        [
            'id' => 'return-form-box_barcode',
            'class' => 'form-control input-lg',
            'data-url' => Url::to('/wms/defacto/return-order-v2/validate-our-box-barcode')
        ]
    ); ?>

    <?= $form->field($returnForm, 'client_box_barcode', [
        'template' => "{label}\n{input-group-begin}{input}{button-right}{input-group-end}\n{hint}\n{error}\n",
        'parts' => [
            '{label}' => '<label for="returnform-client_box_barcode">' . Yii::t('return/forms', 'ШК КОРОБА КЛИЕНТА') . '</label>',
            '{input-group-begin}' => '<div class="input-group">',
            '{input-group-end}' => '</div>',
            '{button-right}' => '<div class="input-group-addon" style="background-color: none; border: none; border-radius: none;" >'.''.'</div>'
        ]
    ])->textInput(
        [
            'id' => 'return-form-client_box_barcode',
            'class' => 'form-control input-lg',
            'data-url' => Url::to('/wms/defacto/return-order-v2/validate-client-box-barcode')
        ]
    ); ?>

    <?php ActiveForm::end(); ?>

    <div class="form-group">
        <?php echo  Html::tag('span', Yii::t('return/buttons', 'Закрыть накладную').'<span id="return-messages-process"> </span>', ['class' => 'btn btn-danger pull-right', 'data-url' => Url::toRoute('confirm-order'), 'style' => ' margin-left:10px;', 'id' => 'return-accept-bt']) ?>
        <?= Html::tag('span', Yii::t('return/buttons', 'Не отсканированные'), ['data-url' => Url::toRoute('print-list-differences'), 'class' => 'btn btn-success', 'id' => 'return-list-differences-bt', 'style' => 'margin-left:10px;']) ?>
        <?= Html::tag('span', Yii::t('return/buttons', 'Не размещенные'), ['data-url' => Url::toRoute('print-unallocated-list'), 'class' => 'btn btn-primary', 'id' => 'return-unallocated-list-bt', 'style' => 'margin-right:10px;']) ?>
        <?= Html::tag('span', Yii::t('return/buttons', 'Размещенные короба'), ['data-url' => Url::toRoute('print-accepted-box'), 'class' => 'btn btn-primary', 'id' => 'return-accepted-list-bt', 'style' => 'margin-right:10px;']) ?>
    </div>
    <div id="countdown" data-on="0"></div>
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
<!--    <div id="return-items" class="table-responsive">
        <table class="table">
            <tr>
                <th><?/*= Yii::t('return/forms', 'Product Barcode'); */?></th>
                <th><?/*= Yii::t('return/forms', 'Product Model'); */?></th>
                <th><?/*= Yii::t('return/forms', 'Expected Qty'); */?></th>
                <th><?/*= Yii::t('return/forms', 'Accepted Qty'); */?></th>
                <th><?/*= Yii::t('return/forms', 'Client box'); */?></th>
            </tr>
            <tbody id="return-item-body"></tbody>
        </table>
    </div>-->
</div>
<!--<iframe style="display: none" name="frame-print-alloc-list" src="#" width="468" height="468">
</iframe>-->