<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\stock\models\ChangeAddressPlaceSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ecommerce-inbound-search">

	<?php $form = ActiveForm::begin([
		'id'=>'change-address-search-form',
		'action' => ['index'],
		'method' => 'get',
	]); ?>

	<table class="table" width="100%" cellspacing="10">
		<tr>
			<td width="10%">
				<?= $form->field($model, 'anyPlace')->label(Yii::t('outbound/forms', 'Короб или место')) ?>
			</td>
			<td width="10%">
				<?= $form->field($model, 'productBarcode')->label(Yii::t('outbound/forms', 'Шк товара')) ?>
			</td>
			<td width="10%">
				<?= $form->field($model, 'created_at')->widget(\kartik\daterange\DateRangePicker::className(),
					[
						'convertFormat'=>true,
						'pluginOptions'=>[
							'locale'=>[
								'separator'=> ' / ',
								'format'=>'Y-m-d',
							]
						]
					]
				) ?>
			</td>
		</tr>
	</table>
	<div class="form-group">
		<?= Html::submitButton(Yii::t('buttons', 'Search'), ['class' => 'btn btn-primary']) ?>
		<?= Html::a(Yii::t('buttons', 'Очистить поиск'), 'index', ['class' => 'btn btn-warning']) ?>
	</div>
	<?php ActiveForm::end(); ?>
</div>