<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel stockDepartment\modules\transportLogistics\models\TlDeliveryProposalDefaultSubRouteSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Tl Delivery Proposal Default Sub Routes');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tl-delivery-proposal-default-sub-route-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create Tl Delivery Proposal Default Sub Route'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'tl_delivery_proposal_default_route_id',
            'client_id',
            'from_point_id',
            'to_point_id',
            // 'created_user_id',
            // 'updated_user_id',
            // 'created_at',
            // 'updated_at',
            // 'deleted',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
