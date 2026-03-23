<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel stockDepartment\modules\transportLogistics\models\TlOutboundRegistrySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('transportLogistics/titles', 'Tl: Outbound Registries');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tl-outbound-registry-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('transportLogistics/buttons', 'Create Tl Outbound Registry'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
<?= $this->render('_search', ['model' => $searchModel])?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => false,
        //'summary' => false,
        'columns' => [
            [
                'attribute'=> 'id',
                'format'=> 'html',
                'options'=>[
                    'width' => '100px'
                ],
                'value' => function ($data) { return Html::tag('a', $data->id, ['href'=>Url::to(['view', 'id' => $data->id]), 'target'=>'_blank']);},

            ],
            [
                'attribute' => 'agent_id',
                'value' => function ($data)  {
                    return $agent = $data->agent ? $data->agent->name : '-';
                },
            ],

            [
                'label' => 'Авто',
                'value' => function ($data)  {
                    return $car = $data->car ? $data->car->getDisplayTitle() : '-';
                },
            ],
            'weight:decimal',
            'volume:decimal',
            'places',

            'driver_name',
//            'driver_phone',
            // 'driver_auto_number',

            // 'extra_fields:ntext',
            // 'created_user_id',
            // 'updated_user_id',
            // 'created_at',
            // 'updated_at',
            // 'deleted',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
