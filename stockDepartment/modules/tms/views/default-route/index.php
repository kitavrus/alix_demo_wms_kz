<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\grid\DataColumn;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel stockDepartment\modules\transportLogistics\models\TlDeliveryProposalDefaultRouteSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('transportLogistics/titles', 'Tl Delivery Proposal Default Routes');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tl-delivery-proposal-default-route-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('transportLogistics/buttons', 'Create Tl Delivery Proposal Default Route'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'id',
//            [
//                'class' => DataColumn::className(),
//                'options' => [
//                    'width' => "200px"
//                ],
//                'attribute' => 'client_id',
//                'format'=> 'html',
//                'value' => function($data) use ($clientArray){
//                    if(isset($clientArray[$data->client_id])){
//                        return Html::tag('a', $clientArray[$data->client_id], ['href'=>Url::to(['/client/default/view', 'id' => $data->client_id]), 'target'=>'_blank']);
//                    }
//                    return Yii::t('titles', 'Not set');
//                },
//                'filterType' => GridView::FILTER_SELECT2,
//                'filterWidgetOptions' => [
//                    'data' => $clientArray,
//                    'options' => [
//                        'placeholder' => Yii::t('transportLogistics/forms', 'Select client')
//                    ],
//
//                ],
//            ],
            [
                'class' => DataColumn::className(),
                'attribute' => 'from_point_id',
                'value' => function ($data) use ($storeArray) {return isset($storeArray[$data->from_point_id]) ? $storeArray[$data->from_point_id] : '-';},
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => $storeArray,
                    'options' => [
                        'placeholder' => Yii::t('transportLogistics/titles', 'Select route'),
                    ],
                ],
            ],
            [
                'class' => DataColumn::className(),
                'attribute' => 'to_point_id',
                'value' => function ($data) use ($storeArray) {return isset($storeArray[$data->to_point_id]) ? $storeArray[$data->to_point_id] : '-';},
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => $storeArray,
                    'options' => [
                        'placeholder' => Yii::t('transportLogistics/titles', 'Select route'),
                    ],
                ],
            ],
            //'created_user_id.username',
            // 'updated_user_id',
            // 'created_at',
            // 'updated_at',
            // 'deleted',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
