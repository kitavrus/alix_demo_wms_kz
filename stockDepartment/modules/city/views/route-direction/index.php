<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\city\models\RouteDirectionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Направления');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="route-directions-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a(Yii::t('app', 'Создать направления'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            [
                'attribute'=> 'base_type',
                'filter'=> $searchModel::getTypeArrayData(),
                'value' => function ($data) { return $data->getValueBaseType(); },
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>