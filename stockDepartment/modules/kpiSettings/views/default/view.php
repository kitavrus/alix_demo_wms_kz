<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\modules\kpiSettings\models\KpiSetting */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('titles', 'Kpi Settings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$clientTitle = '';
if($rClient =  $model->client) {
    $clientTitle = $rClient->title;
}
?>
<div class="kpi-setting-view">

    <h1><?= Html::encode($clientTitle).' / '.Html::encode($model->getOperationTypeValue()).' / '.$model->one_item_time.' '.Yii::t('titles','Sec') ?></h1>

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
//            'id',
//            'client_id',
            'client.title',
//            [
//                'attribute' => 'client_id',
//                'value' => $model->client_id,
//            ],
            [
                'attribute' => 'operation_type',
                'value' => $model->getOperationTypeValue(),
            ],
            [
                'attribute' => 'one_item_time',
                'format' => 'text',
                'value' => $model->one_item_time.' '.Yii::t('titles','Sec'),
            ],
            [
                'attribute' => 'created_user_id',
                'value' => $model::getUserName($model->created_user_id),
            ],
            [
                'attribute' => 'updated_user_id',
                'value' => $model::getUserName($model->updated_user_id),
            ],
            'id',
//            'created_at:datetime',
//            'updated_at:datetime',
        ],
    ]) ?>

</div>
