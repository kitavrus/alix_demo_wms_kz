<?php

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;
use app\modules\intermode\controllers\outbound\domain\OutboundPickingFormAsset;

OutboundPickingFormAsset::register($this);
?>
<div id="messages-container">
	<div id="messages-base-line"></div>
	<?= Alert::widget([
		'closeButton' => false,
		'options' => [
			'id' => 'messages-list',
			'class' => 'alert-info hidden',
		],
		'body' => '<span id="messages-list-body"></span>',
	]);
	?>
</div>
<h1>Фиксируем окончание сборки</h1>
<div class="begin-end-pick-list-form">
	<?php $form = ActiveForm::begin([
			'id' => 'begin-end-pick-list-form',
			'enableClientValidation' => false,
			'validateOnChange' => false,
			'validateOnSubmit' => false,
		]
	); ?>
	<?= $form->field($model, 'picking_list_barcode')->textInput()->label(Yii::t('outbound/forms', 'Picking list barcode')); // ,['template'=>'{input}']  ?>
	<?= $form->field($model, 'employee_barcode')->textInput()->label(Yii::t('outbound/forms', 'Employee barcode')); // , ['template'=>'{input}']  ?>
	<?php ActiveForm::end(); ?>
</div>
