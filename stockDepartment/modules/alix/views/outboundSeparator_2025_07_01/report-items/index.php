<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel stockDepartment\modules\alix\controllers\outboundSeparator\domain\entities\OutboundSeparatorItemsSerach */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Outbound Separator Items';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="outbound-separator-items-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Outbound Separator Items', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'outbound_separator_id',
            'outbound_id',
            'order_number',
            'outbound_box_barcode',
            //'product_barcode',
            //'status',
            //'created_user_id',
            //'updated_user_id',
            //'created_at',
            //'updated_at',
            //'deleted',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
