<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\stock\models\InventorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('inventory/forms', 'Inventories');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="inventory-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <p>
        <?= Html::a(Yii::t('inventory/forms', 'Create Inventory'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute'=>'client_id',
                'value'=>function ($model) {
                    $clientTitle = '';
                    if($rClient =  $model->client) {
                        $clientTitle = $rClient->title;
                    }
                    return $clientTitle;
                },
            ],
            'order_number',
            'expected_qty',
            'accepted_qty',
            [
                'attribute'=>'status',
                'value'=>function ($model) { return $model->getStatusValue(); },
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}'
            ],
        ],
    ]); ?>
</div>