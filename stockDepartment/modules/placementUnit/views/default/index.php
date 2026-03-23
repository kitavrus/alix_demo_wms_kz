<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\placementUnit\models\PlacementUnitSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Placement Units');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="placement-unit-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create Placement Unit'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
//            'id',
//            'client_id',
//            'zone_id',
            'barcode',
            'status',
            'count_unit',
//            'type_inout',
            // 'created_user_id',
            // 'updated_user_id',
            // 'created_at',
             'updated_at:datetime',
            // 'deleted',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>