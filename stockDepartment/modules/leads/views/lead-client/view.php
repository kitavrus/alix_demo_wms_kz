<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\modules\leads\models\ExternalClientLead;

/* @var $this yii\web\View */
/* @var $model common\modules\leads\models\ExternalClientLead */

$this->title = $model->full_name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('leads/titles', 'External Client Leads'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="external-client-lead-view col-md-8">

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
            'full_name',
            [
                'label' => Yii::t('client/titles', 'Client type'),
                'value' => $model->getClientTypeValue(),
            ],
            [
                'attribute' => 'legal_company_name',
                'value' => $model->legal_company_name,
                'visible' => $model->client_type == ExternalClientLead::CLIENT_TYPE_CORPORATE ? true : false,
            ],
            'phone',
            'email:email',

            [
                'attribute' => 'status',
                'value' => $model->getClientStatusValue(),
            ],
           [
                'label' => Yii::t('forms', 'Updated User ID'),
                'value' => is_object($model->updatedUser) ? $model->updatedUser->username : Yii::t('titles', 'Not set'),
            ],
            'created_at:datetime',
            //'updated_at:datetime',
        ],
    ]) ?>

</div>
