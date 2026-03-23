<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\modules\leads\models\TtCompanyLead */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('leads/titles', 'Company lead'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tt-company-lead-view col-md-8">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('buttons', 'Edit'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('buttons', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('titles', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'customer_name',
            'customer_company_name',
            'customer_position',
            'customer_phone',
            'customer_email:email',
            [
                'attribute' => 'status',
                'value' => $model->getStatusValue(),
            ],
            [
                'attribute' => 'cooperation_type_1',
                'value' => $model->cooperation_type_1 ? Yii::t('leads/titles', 'Yes') : Yii::t('leads/titles', 'No'),
            ],
            [
                'attribute' => 'cooperation_type_2',
                'value' => $model->cooperation_type_2 ? Yii::t('leads/titles', 'Yes') : Yii::t('leads/titles', 'No'),
            ],
            [
                'attribute' => 'cooperation_type_3',
                'value' => $model->cooperation_type_3 ? Yii::t('leads/titles', 'Yes') : Yii::t('leads/titles', 'No'),
            ],
            'customer_comment',
            'createdUser.username',
            'updatedUser.username',
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

</div>
