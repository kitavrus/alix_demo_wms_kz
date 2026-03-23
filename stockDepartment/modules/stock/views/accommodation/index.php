<?php
/**
 * Created by PhpStorm.
 * User: Igor
 * Date: 15.01.15
 * Time: 18:02
 */

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;
use stockDepartment\modules\stock\models\AccommodationForm;


/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
?>

<?php $this->title = Yii::t('stock/titles', 'Accommodation');?>

<div id="messages-container">
	<div id="messages-base-line"></div>
	<?= Alert::widget([
		'options' => [
			'id' => 'messages-list',
			'class' => 'alert-info hidden',
		],
		'body' => '<span id="messages-list-body"></span>',
	]);
	?>
</div>

<div class="stock-accommodation-form">
	<?php $form = ActiveForm::begin([
			'id'=>'stock-accommodation-process-form',
			'enableClientValidation'=>false,
			'validateOnChange'=>false,
			'validateOnSubmit'=>false,
		]
	); ?>

	<?= $form->field($af, 'type',['labelOptions'=>['id'=>'type-label']])->dropDownList(
		AccommodationForm::getTypeArray(),
		[
		 'prompt'=>'',
		 'id'=>'stock-accommodation-type',
		]
	); ?>

	<?= $form->field($af, 'from',['labelOptions'=>['id'=>'from-label']])->textInput(); ?>

	<?= $form->field($af, 'to',['labelOptions'=>['id'=>'to-label']])->textInput(); ?>

	<?php ActiveForm::end(); ?>

	<div class="container" id="container-button">
		<div class="row">
			<button type="button" class="btn btn-default key">-</button>
			<button type="button" class="btn btn-default key">0</button>
			<button type="button" class="btn btn-default key">1</button>
			<button type="button" class="btn btn-default key">2</button>
			<button type="button" class="btn btn-default key">3</button>
			<button type="button" class="btn btn-default key">4</button>
			<button type="button" class="btn btn-default key">5</button>
			<button type="button" class="btn btn-default key">6</button>
			<button type="button" class="btn btn-default key">7</button>
			<button type="button" class="btn btn-default key">8</button>
			<button type="button" class="btn btn-default key">9</button>
			<button type="button" class="btn btn-danger  key">enter</button>
			<button type="button" class="btn btn-warning key">del</button>
		</div>
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
</div>

<script type="text/javascript">
//	$(function(){
		var labelArray = <?= \yii\helpers\Json::encode($af->labelTranslateArray()); ?>
//	});
</script>