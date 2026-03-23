<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\ecommerce\entities\EcommerceBarcodeManagerSerach */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Ecommerce Barcode Managers';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ecommerce-barcode-manager-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Ecommerce Barcode Manager', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'barcode_prefix',
            'title',
            'counter',
            'status',
            //'created_user_id',
            //'updated_user_id',
            //'created_at',
            //'updated_at',
            //'deleted',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
