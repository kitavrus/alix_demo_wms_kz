<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel stockDepartment\modules\transportLogistics\models\TlAgentEmployeesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('forms', 'Tl Agent Employees');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tl-agent-employees-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('forms', 'Create {modelClass}', [
    'modelClass' => 'Tl Agent Employees',
]), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'tl_agent_id',
            'user_id',
            'name',
            'first_name',
            // 'middle_name',
            // 'last_name',
            // 'phone',
            // 'phone_mobile',
            // 'email:email',
            // 'manager_type',
            // 'status',
            // 'created_user_id',
            // 'updated_user_id',
            // 'created_at',
            // 'updated_at',
            // 'deleted',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
