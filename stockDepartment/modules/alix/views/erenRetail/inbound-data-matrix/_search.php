<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\dataMatrix\models\InboundDataMatrixSerach */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="inbound-data-matrix-search">
	<?php $form = ActiveForm::begin([
		'action' => ['index'],
		'method' => 'get',
	]); ?>
	<table class="table" width="100%" cellspacing="10" cellpadding="100">
		<tr>
			<td width="10%">
				<?= $form->field($model, 'inbound_id')
				         ->dropDownList($inboundOrdersSearch,['prompt' =>Yii::t('transportLogistics/titles', 'Выбрать накладную')]) ?>
			</td>
			<td width="10%">
				<?= $form->field($model, 'product_barcode') ?>
			</td>
			<td width="10%">
				<?= $form->field($model, 'product_model') ?>
			</td>
			<td width="10%">
				<?php echo $form->field($model, 'data_matrix_code') ?>
			</td>
			<td width="10%">
				<?php echo $form->field($model, 'status') ?>
			</td>
			<td width="10%">
				<?php echo $form->field($model, 'print_status') ?>
			</td>
		</tr>
	</table>
	<div class="form-group">
		<?= Html::submitButton(Yii::t('buttons', 'Search'), ['class' => 'btn btn-primary']) ?>
		<?= Html::a(Yii::t('buttons', 'Очистить поиск'), 'index', ['class' => 'btn btn-warning']) ?>
	</div>
	<?php ActiveForm::end(); ?>
</div>
