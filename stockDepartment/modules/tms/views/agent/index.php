<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\transportLogistics\transportLogistics;
use common\modules\transportLogistics\models\TlAgents;

/* @var $this yii\web\View */
/* @var $searchModel stockDepartment\modules\transportLogistics\models\TlAgentsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('transportLogistics/titles', 'Tl Agents');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tl-agents-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('transportLogistics/buttons', 'Create Agent'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
//            'title',
            'phone',
            'phone_mobile',
            [
                'attribute' => 'status',
                'value' => function($data){
                    return TlAgents::getStatusArray($data->status);
                }
            ],
            [
                'attribute' => 'flag_nds',
                'value' => function($data){
                    return $data->getNdsFlagValue();
                }
            ],
            [
                'attribute' => 'payment_period',
                'value' => function($data){
                    return $data->getPaymentPeriodValue();
                }
            ],
            // 'description:ntext',
            // 'status',
            // 'contact_first_name',
            // 'contact_middle_name',
            // 'contact_last_name',
            // 'contact_phone',
            // 'contact_phone_mobile',
            // 'contact_first_name2',
            // 'contact_middle_name2',
            // 'contact_last_name2',
            // 'contact_phone2',
            // 'contact_phone_mobile2',
            // 'address_title',
            // 'country',
            // 'region',
            // 'city',
            // 'zip_code',
            // 'street',
            // 'house',
            // 'entrance',
            // 'flat',
            // 'intercom',
            // 'floor',
            // 'comment:ntext',
            // 'created_at',
            // 'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
