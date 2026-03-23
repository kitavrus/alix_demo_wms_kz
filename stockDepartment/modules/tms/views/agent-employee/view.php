<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\modules\transportLogistics\models\TlAgentEmployees */

$this->title = $model->username;
$this->params['breadcrumbs'][] = ['label' => Yii::t('forms', 'Tl Agent Employees'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tl-agent-employees-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('forms', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('forms', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('forms', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
//            'id',
            'tl_agent_id',
//            'user_id',
            'username',
            'first_name',
            'middle_name',
            'last_name',
            'phone',
            'phone_mobile',
            'email:email',
            'manager_type',
            'status',
            'created_user_id',
            'updated_user_id',
            'created_at',
            'updated_at',
//            'deleted',
        ],
    ]) ?>

</div>
