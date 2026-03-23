<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\city\city;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\city\models\CountrySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = city::t('titles', 'Countries');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="country-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(city::t('buttons', 'Create Country'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            'comment:ntext',
            'createdUser.username',
            'updatedUser.username',
            // 'created_at',
            // 'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
