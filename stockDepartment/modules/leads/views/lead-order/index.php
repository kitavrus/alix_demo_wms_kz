<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use common\modules\city\models\City;
use kartik\grid\EditableColumn;
use kartik\grid\DataColumn;
/* @var $this yii\web\View */
/* @var $searchModel app\modules\leads\models\TransportationOrderLeadSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('leads/titles', 'Transportation Order Leads');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="transportation-order-lead-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a(Yii::t('leads/buttons', 'Reset filter'), ['index'], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'order_number',
            'customer_name',
            [
                'attribute'=>'from_city_id',
                'filter'=> City::getArrayData(),
                'value'=>function($data){
                    if(is_object($data->fromCity)){
                        return $data->fromCity->name;
                    }
                    return Yii::t('titles', 'Not set');
                }
            ],
            [
                'attribute'=>'to_city_id',
                'filter'=> City::getArrayData(),
                'value'=>function($data){
                    if(is_object($data->toCity)){
                        return $data->toCity->name;
                    }
                    return Yii::t('titles', 'Not set');
                }
            ],
            'places',
            'weight',
            'volume',
            'cost_vat:currency',
            [
                'attribute'=>'source',
                'filter'=> $searchModel->getSourceArray(),
                'value'=>function($data){
                    return $data->getSourceValue();
                }
            ],
            [
                'attribute'=>'status',
                'filter'=> $searchModel->getStatusArray(),
                'value'=>function($data){
                    return $data->getStatusValue();
                }
            ],
            'created_at:datetime',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
