<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel stockDepartment\modules\employee\models\EmployeeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('titles', 'Employees');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="employees-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('titles', 'Create Employee'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            //'user_id',
            'username',
            //'password',
            //'title',
            'first_name',
            'middle_name',
            'last_name',
            'barcode',
            // 'phone',
            // 'phone_mobile',
             'email:email',
            [
             'attribute' => 'manager_type',
             'value' => function ($model) {
                    return $model->getType();
                },
             'filter' => $searchModel::getTypeArray(),
            ],
            // 'department',
            [
                'attribute' => 'status',
                'value' => function ($model) {
                    return $model->getStatus();
                },
                'filter' => $searchModel::getStatusArray(),
            ],
             //'createdUser.username',
            // 'updated_user_id',
            // 'created_at',
            // 'updated_at',
            // 'deleted',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
