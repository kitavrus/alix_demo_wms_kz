<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\leads\models\TtCompanyLeadSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('leads/titles', 'Company lead');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tt-company-lead-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'customer_name',
            'customer_company_name',
            'customer_position',
            'customer_phone',
            'customer_email:email',
            [
                'attribute'=>'status',
                'filter'=> $searchModel->getStatusArray(),
                'value'=>function($data){
                    return $data->getStatusValue();
                }
            ],
            [
                'attribute' => 'cooperation_type_1',
                'value' => function ($data) {
                            return $data->cooperation_type_1 ? Yii::t('leads/titles', 'Yes') : Yii::t('leads/titles', 'No');
                         },
            ],
            [
                'attribute' => 'cooperation_type_2',
                'value' => function ($data) {
                    return $data->cooperation_type_2 ? Yii::t('leads/titles', 'Yes') : Yii::t('leads/titles', 'No');
                },
            ],
            [
                'attribute' => 'cooperation_type_3',
                'value' => function ($data) {
                    return $data->cooperation_type_3 ? Yii::t('leads/titles', 'Yes') : Yii::t('leads/titles', 'No');
                },
            ],
            // 'customer_comment',
            // 'created_user_id',
            // 'updated_user_id',
            'created_at:datetime',
            // 'updated_at',
            // 'deleted',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
