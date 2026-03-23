<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel stockDepartment\modules\inbound\models\InboundOrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('forms', 'Inbound Orders');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="inbound-order-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('forms', 'Create {modelClass}', [
    'modelClass' => 'Inbound Order',
]), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'client_id',
            'supplier_id',
            'warehouse_id',
            'order_number',
            // 'order_type',
            // 'status',
            // 'expected_qty',
            // 'accepted_qty',
            // 'accepted_number_places_qty',
            // 'expected_number_places_qty',
            // 'expected_datetime:datetime',
            // 'begin_datetime:datetime',
            // 'end_datetime:datetime',
            // 'created_user_id',
            // 'updated_user_id',
            // 'created_at',
            // 'updated_at',
            // 'deleted',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
