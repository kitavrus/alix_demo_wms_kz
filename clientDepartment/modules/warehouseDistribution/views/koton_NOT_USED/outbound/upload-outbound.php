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
use yii\bootstrap\Modal;
?>

<?php $this->title = Yii::t('outbound/titles', 'Upload Outbound Order'); ?>
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
<h1><?= $this->title;?> </h1>
<div class="upload-outbound-form">
    <?php $form = ActiveForm::begin([
            'id' => 'upload-outbound-form',
            'enableClientValidation' => false,
            'validateOnChange' => false,
            'validateOnSubmit' => false,
            'options' => ['enctype' => 'multipart/form-data']
        ]
    ); ?>

    <?= $form->field($model, 'from_point')->dropDownList($filterWidgetOptionDataRoute, ['prompt'=>Yii::t('titles', 'Select')]); ?>

    <?= $form->field($model,'to_point')->dropDownList($filterWidgetOptionDataRoute, ['prompt'=>Yii::t('titles', 'Select')]); ?>
<!--    --><?//= Html::tag('span', Yii::t('return/buttons', 'Generate new'), ['data-url' => Url::toRoute('get-inbound-orders-number'), 'class' => 'btn btn-success', 'id' => 'return-process-form-generate-new-bt', 'style' => 'margin-left:10px;']) ?>
<!--    --><?//= Html::tag('span', Yii::t('return/buttons', 'Delete order'), ['data-url' => Url::toRoute('delete-inbound-order'), 'class' => 'btn btn-danger hidden', 'id' => 'return-delete-inbound-order-bt', 'style' => 'margin-right:10px;']) ?>
<!--    --><?//= Html::tag('span', Yii::t('return/buttons', 'Accept inbound order'), ['data-url' => Url::toRoute('accept-inbound-order'), 'class' => 'btn btn-warning pull-right', 'id' => 'return-process-form-accept-inbound-order-bt', 'style' => 'margin-right:10px;']) ?>
    <?php if (!$previewData) {?>
        <?= $form->field($model, 'file')->fileInput() ?>

        <div class="row" style="margin: 20px 1px">
            <?= Html::submitButton(Yii::t('return/buttons', 'Upload'), ['class'=>'btn btn-primary']) ?>
        </div>
    <?php }?>

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

</div>
<?php Modal::begin([
    //'header' => '<h4 id="delivery-proposal-index-header"></h4>',
    'id' => 'loading-modal',
    'closeButton' => false,
    'options' => [
        'data-backdrop' => 'static',
        'data-keyboard' => 'false',
    ],
]); ?>
<?= "<div id='loading-modal-content'>Идет обработка данных, пожалуйста подождите...</div>"; ?>
<?php Modal::end(); ?>

<?php echo $this->render('_order_items', ['previewData' => $previewData, 'itemsQty' => $itemsQty,])?>

<script type="text/javascript">
    $(function(){
        var  body = $('body');

        body.on('click', '#koton-reset-upload-bt', function(){
            window.location.href = $(this).data('url');
        });

        body.on('click', '#koton-confirm-upload-btn', function(){
            var from_id = $('#kotonoutboundform-from_point').val(),
                to_id = $('#kotonoutboundform-to_point').val();

            if(from_id.length < 1 || to_id.length < 1){
                alert('Необходимо выбрать точку отправления и точку доставки');
                return false;
            }

            if(confirm('Вы уверены что хотите создать расходную накладную с указанными данными?')){
                $('#loading-modal').modal('show');
                $.post($(this).data('url'), {'from' : from_id, 'to' : to_id}, function (result) {

                }, 'json')
            }

        });
    })
 </script
