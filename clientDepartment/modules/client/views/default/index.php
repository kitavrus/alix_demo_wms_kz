<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel clientDepartment\modules\client\models\ClientSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('forms', 'Clients');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('forms', 'Create {modelClass}', [
    'modelClass' => 'Client',
]), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

//            'id',
            'user_id',
            'username',
            'title',
            'first_name',
            // 'middle_name',
            // 'last_name',
             'phone',
            // 'phone_mobile',
             'email:email',
            // 'status',
             'created_user_id',
             'updated_user_id',
             'created_at',
             'updated_at',
            // 'deleted',

            ['class' => 'yii\grid\ActionColumn',
                'template'=>'{view} {update} {delete}',
                'buttons'=>[
                    'delete'=> function ($url, $model, $key) {
                        $a =  Html::a(Yii::t('buttons', 'Delete'), ['delete', 'id' => $model->id], [
                            'class' => 'btn btn-danger btn-grid-action-column',
                            'data' => [
                                'confirm' => Yii::t('titles', 'Are you sure you want to delete this item?'),
                                'method' => 'post',
                            ],
                        ]);
                        return  $a.'<br />';
                    },
                    'update'=> function ($url, $model, $key) {
                        return  Html::a(Yii::t('buttons','Edit'), $url,['class'=>'btn btn-warning btn-grid-action-column']).'<br />';
                    },
                    'view'=> function ($url, $model, $key) {
                        return Html::a(Yii::t('buttons','View'), $url,['class'=>'btn btn-primary btn-grid-action-column']).'<br />';
                    },
                ]
            ],
        ],
    ]); ?>

</div>
