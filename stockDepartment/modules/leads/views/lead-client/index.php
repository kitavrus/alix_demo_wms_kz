<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\leads\models\ExternalClientLeadSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('leads/titles', 'External Client Leads');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="external-client-lead-index">

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
            'full_name',
            //'client_address',
            'phone',
            'email:email',
            [
                'label' => Yii::t('leads/forms', 'Client type'),
                'attribute' =>'client_type',
                'filter'=>$searchModel->getClientTypeArray(),
                'value' => function($data){
                    return $data->getClientTypeValue();
                }
            ],
            // 'company_name',
            [
                'attribute' =>'status',
                'filter'=>$searchModel->getClientStatusArray(),
                'value' => function($data){
                    return $data->getClientStatusValue();
                }
            ],
            // 'created_user_id',
            // 'updated_user_id',
            'created_at:datetime',
            // 'updated_at',
            // 'deleted',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
