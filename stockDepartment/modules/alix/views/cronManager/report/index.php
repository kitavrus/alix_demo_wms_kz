<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel stockDepartment\modules\alix\controllers\cronManager\domains\cron_manager\CronManagerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Cron Managers';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cron-manager-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'name',
            'order_id',
            'status',
            'type',
            'total_counter',
            'result_message',
			[
				'attribute'=>'created_at',
				'format' => 'raw',
				'value' => function ($model) {
					return $model->created_at ? Yii::$app->formatter->asDatetime($model->created_at) : '-';
				}
			],
        ],
    ]); ?>
</div>