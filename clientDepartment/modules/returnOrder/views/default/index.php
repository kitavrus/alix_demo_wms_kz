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
use common\modules\store\models\Store;
use common\modules\transportLogistics\components\TLHelper;
use app\modules\returnOrder\assets\ReturnAsset;
/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model app\modules\returnOrder\models\ReturnForm */
?>
<?php ReturnAsset::register($this); ?>

<?php $this->title = Yii::t('return/titles', 'Return Orders'); ?>
<div id="messages-container">
    <div id="messages-base-line"></div>
<!--    --><?//= Alert::widget([
//        'options' => [
//            'id' => 'messages-list',
//            //'class' => 'alert-info hidden',
//        ],
//        'body' => '<span id="messages-list-body"></span>',
//    ]);
//    ?>
</div>
<div class="return-order-process-form">
    <?php $form = ActiveForm::begin([
            'id' => 'return-process-form',
//            'enableClientValidation' => false,
            'validateOnChange' => false,
//            'validateOnSubmit' => false,
            'options' => ['enctype' => 'multipart/form-data']
        ]
    ); ?>

    <?= $form->field($model, 'store_id')->dropDownList($filterWidgetOptionDataRoute, ['prompt'=>Yii::t('return/titles', 'Select store'),'data'=>['url'=>Url::toRoute('get-inbound-orders-number')]]); ?>

    <?= $form->field($model,'inbound_order_number')->dropDownList($inboundOrderNumberList, ['prompt'=>Yii::t('return/titles', 'Select order'),'data'=>['url'=>Url::toRoute('get-orders-items-by-party-id')]]); ?>
    <?= Html::tag('span', Yii::t('return/buttons', 'Generate new'), ['data-url' => Url::toRoute('get-inbound-orders-number'), 'class' => 'btn btn-success', 'id' => 'return-process-form-generate-new-bt', 'style' => 'margin-left:10px;']) ?>
    <?= Html::tag('span', Yii::t('return/buttons', 'Delete order'), ['data-url' => Url::toRoute('delete-inbound-order'), 'class' => 'btn btn-danger hidden', 'id' => 'return-delete-inbound-order-bt', 'style' => 'margin-right:10px;']) ?>
    <?= Html::tag('span', Yii::t('return/buttons', 'Accept inbound order'), ['data-url' => Url::toRoute('accept-inbound-order'), 'class' => 'btn btn-warning pull-right', 'id' => 'return-process-form-accept-inbound-order-bt', 'style' => 'margin-right:10px;']) ?>

<br />
<br />
    <?= $form->field($model, 'file[]')->fileInput(['multiple' => true]) ?>

    <div class="row" style="margin: 20px 1px">
        <?= Html::submitButton(Yii::t('return/buttons', 'Upload').'<span id="return-process-form-upload-message"></span>', ['class'=>'btn btn-primary','id'=>'return-process-form-upload-submit-bt']) ?>
    </div>
    <?php ActiveForm::end(); ?>
    <div id="error-container">
        <div id="error-base-line"></div>
<!--        --><?//= Alert::widget([
//            'options' => [
//                'id' => 'error-list',
//                'class' => 'alert-danger hidden',
//            ],
//            'body' => '',
//        ]);
//        ?>
    </div>
    <div id="inbound-items" class="table-responsive">
        <table class="table">
            <tr>
                <th><?= Yii::t('return/forms', 'Box Barcode'); ?></th>
                <th><?= Yii::t('return/forms', 'Expected Qty'); ?></th>
                <th>-</th>
            </tr>
            <tbody id="inbound-item-body"></tbody>
        </table>
    </div>
</div>