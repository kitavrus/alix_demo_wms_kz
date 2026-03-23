<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel stockDepartment\modules\wms\models\defacto\OutboundBoxesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Outbound Boxes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="outbound-boxes-index">
	<h1><?= Html::encode($this->title) ?></h1>
	<?php echo $this->render('_search', ['model' => $searchModel]); ?>
	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'columns' => [
			// 'id',
			'our_box',
			'client_box',
			'client_extra_json:ntext',
		],
	]); ?>
</div>