<?php

use yii\helpers\Html;
//use yii\grid\GridView;
use kartik\grid\GridView;
//use kartik\grid\EditableColumn;
use kartik\grid\DataColumn;

/* @var $this yii\web\View */
/* @var $searchModel stockDepartment\modules\kpiSettings\models\KpiSettingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('titles', 'Kpi Settings');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="kpi-setting-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('buttons', 'Create Setting'), ['create'], ['class' => 'btn btn-success']) ?>

        <?= Html::a(Yii::t('buttons', 'Clear search'), ['index'], ['class' => 'btn btn-primary','style'=>'float:right; margin-left:10px;']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],
            //'id',
            [
                'class' => DataColumn::className(),
                'attribute' => 'client_id',
                'value' => 'client.title',
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => $searchModel->getClientArray(),
                    'options' => [
                        'placeholder' => Yii::t('transportLogistics/forms', 'Select client')
                    ],
                ],
            ],
            [
                'attribute' => 'operation_type',
                'value' => function($model){ return $model->getOperationTypeValue(); },
            ],
            [
                'attribute' => 'one_item_time',
                'format' => 'text',
                'value' => function($model){ return $model->one_item_time.' '.Yii::t('forms','Sec'); },
            ],
//            [
//                'attribute' => 'created_user_id',
//                'value' => function($model){ return $model::getUserName($model->created_user_id); },
//            ],
//            [
//                'attribute' => 'updated_user_id',
//                'value' => function($model){ return $model::getUserName($model->updated_user_id); },
//            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template'=>'{view} {update}'
            ],
        ],
    ]); ?>

</div>
