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

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $orderNumberArray common\modules\inbound\models\InboundOrder */
/* @var $clientsArray common\modules\client\models\Client */
/* @var $inboundForm stockDepartment\modules\inbound\models\InboundForm */

?>
<?php $this->title = "Приходной накладной для Defacto Ecommerce"; ?>
<h1><?= $this->title ?></h1>


<?php if(!empty($provider)) { ?>
	<?= \yii\grid\GridView::widget([
		'tableOptions' => ['class' => 'table table-bordered'],
		'id' => 'grid-view-inbound-order-items',
		'dataProvider' => $provider,
		'layout'=>'{items}',
		'pager'=>false,
		'sorter'=>false,
		'columns' => [
			'AppointmentBarcode',
			'AppointmentDate',
			[
				'attribute'=>'actions',
				'label' => Yii::t('outbound/forms','Actions'),
				'format' => 'raw',
				'value' => function($model) {
					$bt  = Html::a(
							"Накладная прибыла на склад",
							['send-read-appointments', 'AppointmentBarcode' => $model['AppointmentBarcode']],
							['class' => 'btn btn-danger',
								'data' => [
									'confirm' => Yii::t('app', 'Вы действительно загрузили накладную?'),
								]
							]);
					return $bt;
				},
			]
		],
	]); ?>
<?php } ?>