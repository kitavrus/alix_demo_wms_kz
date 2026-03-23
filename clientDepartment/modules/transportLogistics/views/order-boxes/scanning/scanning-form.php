<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 02.02.15
 * Time: 14:58
 */
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;
use yii\helpers\Url;
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
			'id' => 'scanning-form',
			'enableClientValidation' => false,
			'validateOnChange' => false,
			'validateOnSubmit' => false,
		]
	); ?>

	<?= $form->field($modelForm, 'delivery_proposal_id')->hiddenInput()->label(false); ?>
	<?= $form->field($modelForm, 'employee_name')->textInput(['class' => 'form-control input-lg']); ?>
	<?= $form->field($modelForm, 'box_barcode', [
		'template' => "{label}\n{input-group-begin}{counter}{input}{input-group-end}\n{hint}\n{error}\n",
		'parts' => [
			'{label}' => '<label for="scanningform-box_barcode">' . Yii::t('outbound/forms', 'Box Barcode') . '</label>',
			'{input-group-begin}' => '<div class="input-group">',
			'{input-group-end}' => '</div>',
			'{counter}' => '<div class="input-group-addon" style="font-size: 20px;">' . Yii::t('outbound/forms', 'Коробов') . ': <strong id="count-box" >0</strong></div>',
		]
	])->textInput(['class' => 'form-control input-lg'])->label(Yii::t('outbound/forms', 'Box Barcode')) ?>

	<?php ActiveForm::end(); ?>
	<div class="row" style="margin: 20px 1px">
		<?= Html::a(
				Yii::t('outbound/buttons', 'Print box label'),
				Url::toRoute(['print-box-label',"id"=>$modelForm->delivery_proposal_id]),
			['class' => 'btn btn-primary']
		) ?>
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
				<th><?= Yii::t('outbound/forms', 'Box Barcode'); ?></th>
			</tr>
			<tbody id="outbound-item-body"><?= $boxes ?></tbody>
		</table>
	</div>
	<script type="text/javascript">
		$(function(){
			var b = $('body');

			b.on('click',"#scanningform-employee_name,#scanningform-box_barcode", function (e) {
				$(this).focus().select();
			});

			// EMPLOYEE NAME
			b.on('keyup',"#scanningform-employee_name", function (e) {
				if (e.which == 13) {
					var url = '<?= Url::toRoute('employee-name'); ?>',
						me = $(this),
						form = $('#scanning-form');

					errorBase.setForm(form);

					$.post(url, form.serialize(),function (result) {
						if (result.success == 'N') {
							errorBase.eachShow(result.errors);
							me.focus().select();
						} else {
							errorBase.hidden();
							$('#scanningform-box_barcode').focus().select();
						}
					}, 'json').fail(function (xhr, textStatus, errorThrown) {
					});
				}
				e.preventDefault();
			});

			// BOX BARCODE
			b.on('keyup',"#scanningform-box_barcode", function (e) {

				if (e.which == 13) {

					var url = '<?= Url::toRoute('box-barcode'); ?>',
						me = $(this),
						form = $('#scanning-form');

					errorBase.setForm(form);

					$.post(url, form.serialize(),function (result) {

						if (result.success == 'N') {
							errorBase.eachShow(result.errors);
							me.focus().select();
						} else {
							errorBase.hidden();

							$('#scanningform-box_barcode').focus().select();
							$('#count-box').html(result.countBoxes);
							$('#outbound-item-body').html(result.boxes);

						}
					}, 'json').fail(function (xhr, textStatus, errorThrown) {
					});
				}
				e.preventDefault();
			});

		});
	</script>