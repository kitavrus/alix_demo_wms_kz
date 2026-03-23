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
	
	<?= $form->field($model,'outbound_order_number',[
			'errorOptions' => [
				'encode' => false,
				'class' => 'help-block'
			]
		]
	)->textInput()?>
	<?= $form->field($model, 'description',
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
	'id' => 'loading-modal',
	'closeButton' => false,
	'options' => [
		'data-backdrop' => 'static',
		'data-keyboard' => 'false',
	],
]); ?>
<?= "<div id='loading-modal-content'>Идет обработка данных, пожалуйста подождите...</div>"; ?>
<?php Modal::end(); ?>

<?php echo $this->render('_outbound_items', ['previewData' => $previewData])?>

<script type="text/javascript">
    $(function(){
        var  body = $('body');

        body.on('click', '#eren-retail-reset-upload-bt', function(){
            window.location.href = $(this).data('url');
        });

        body.on('click', '#eren-retail-confirm-upload-btn', function(){
            var from_id = $('#erenretailoutboundform-from_point').val(),
                to_id = $('#erenretailoutboundform-to_point').val(),
	            description =$('#erenretailoutboundform-description').val(),
	            outbound_order_number=$('#erenretailoutboundform-outbound_order_number').val();

            if(from_id.length < 1 || to_id.length < 1){
                alert('Необходимо выбрать точку отправления и точку доставки');
                return false;
            }

            if(confirm('Вы уверены что хотите создать расходную накладную с указанными данными?')){
                $('#loading-modal').modal('show');
                $.post($(this).data('url'), {
	                'from' : from_id,
	                'to' : to_id,
	                'description': description,
	                'outbound_order_number':outbound_order_number
					}, function (result) {

                }, 'json')
            }

        });
    })
 </script
