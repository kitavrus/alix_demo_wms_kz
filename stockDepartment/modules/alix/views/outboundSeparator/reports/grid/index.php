<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel stockDepartment\modules\alix\controllers\outboundSeparator\domain\entities\OutboundSeparatorSerach */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Outbound Separators';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="outbound-separator-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

<!--    <p>-->
<!--        --><?//= Html::a('Create Outbound Separator', ['create'], ['class' => 'btn btn-success']) ?>
<!--    </p>-->

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
			[
				'attribute'=> 'order_number',
				'format'=> 'html',
				'value' => function ($data) { return Html::tag('a', $data->order_number, ['href'=>Url::to(['view', 'id' => $data->id]), 'target'=>'_blank']);},

			],
            'comments',
            'status',
            'path_to_file:ntext',
            //'created_user_id',
            //'updated_user_id',
            //'created_at',
            //'updated_at',
            //'deleted',

//            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
