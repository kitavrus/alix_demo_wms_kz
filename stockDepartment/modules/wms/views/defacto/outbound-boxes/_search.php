<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model stockDepartment\modules\wms\models\defacto\OutboundBoxesSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="outbound-boxes-search">
	<?php $form = ActiveForm::begin([
		'action' => ['index'],
		'method' => 'get',
	]); ?>
	<?= $form->field($model, 'our_box')->label('Наш шк короба') ?>
	<?= $form->field($model, 'client_box')->label('LC короба') ?>
	<div class="form-group">
		<?= Html::submitButton('Искать', ['class' => 'btn btn-primary']) ?>
		<?= Html::a(Yii::t('buttons', 'Очистить поиск'), "/wms/defacto/outbound-boxes/index", ['class' => 'btn btn-default']) ?>
	</div>
	<?php ActiveForm::end(); ?>
</div>