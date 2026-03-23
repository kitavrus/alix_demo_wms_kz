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
use common\modules\inbound\models\InboundOrder;
?>

<?php $this->title = Yii::t('outbound/titles', 'Загрузить приходную накладную'); ?>
<div id="messages-container">
    <div id="messages-base-line"></div>
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
		<?= $form->field($model,'order_type')
	         ->dropDownList([
	         		InboundOrder::ORDER_TYPE_INBOUND=>"приход",
		            InboundOrder::ORDER_TYPE_RETURN=>"возврат"
	         ], [
	         		'prompt'=>Yii::t('titles', 'Select')
				]
		); ?>


	<?= $form->field($model, 'orderNumber',
		[
			'errorOptions' => [
				'encode' => false,
				'class' => 'help-block'
			]
		]
	)->textInput() ?>
	<?= $form->field($model, 'comment',
		[
			'errorOptions' => [
				'encode' => false,
				'class' => 'help-block'
			]
		]
	)->textInput() ?>
    <?php if (empty($previewData->expectedTotalProductQty)) {?>
        <?= $form->field($model, 'file')->fileInput() ?>
        <div class="row" style="margin: 20px 1px">
            <?= Html::submitButton(Yii::t('return/buttons', 'Upload'), ['class'=>'btn btn-primary']) ?>
        </div>
    <?php }?>

    <?php ActiveForm::end(); ?>
    <div id="error-container">
        <div id="error-base-line"></div>
    </div>

</div>
<?php Modal::begin([
    'id' => 'loading-modal',
    'closeButton' => false,
    'options' => [
        'data-backdrop' => 'static',
        'data-keyboard' => 'false',
    ],
]); ?>
<?= "<div id='loading-modal-content'>Идет обработка данных, пожалуйста подождите...</div>"; ?>
<?php Modal::end(); ?>

<?php echo $this->render('_order_items', ['previewData' => $previewData])?>

<script type="text/javascript">
    $(function(){
        var  body = $('body');

        body.on('click', '#eren-retail-reset-upload-bt', function(){
            window.location.href = $(this).data('url');
        });

        body.on('click', '#eren-retail-confirm-upload-btn', function(){
            var ordernumber = $('#erenretailinboundform-ordernumber').val(),
	            comment = $('#erenretailinboundform-comment').val(),
				  order_type = $('#erenretailinboundform-order_type').val();

            if(ordernumber.length < 1){
                alert('Необходимо указать номер накладной');
                return false;
            }

            if(confirm('Вы уверены что хотите создать приходную накладную с указанными данными?')){
                $('#loading-modal').modal('show');
                $.post($(this).data('url'), {'ordernumber' : ordernumber, 'comment' : comment, 'order_type' : order_type}, function (result) {

                }, 'json')
            }

        });
    })
 </script
