<?php

use yii\helpers\Html;
use app\modules\city\city;
use kartik\grid\GridView;
use kartik\grid\DataColumn;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\city\models\CitySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = city::t('titles', 'Cities');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="city-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(city::t('buttons', 'Create City'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

//            'id',
            'name',
            [
                'class' => DataColumn::className(),
                'attribute' => 'region_id',
                'value' => 'region.name',
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                        'data' => \common\modules\city\models\Region::getArrayData(),
//                    'data' => $searchModel::getRouteFromTo(),
                    'options' => [
                        'placeholder' => Yii::t('forms', 'Select region'),
                    ],

                ],
            ],
//            'comment:ntext',
//            'created_user_id',
            // 'updated_user_id',
             'created_at:datetime',
             'updated_at:datetime',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
