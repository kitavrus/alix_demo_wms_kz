<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\modules\audit\models\Audit;

/* @var $this yii\web\View */
/* @var $model stockDepartment\modules\Bookkeeper\models\Bookkeeper */

$this->title = ($model->type_id == $model::TYPE_PLUS ? Yii::t('app', 'ПРИХОД') : Yii::t('app', 'РАСХОД') ).' = '.Yii::$app->formatter->asCurrency($model->price);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Учет'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bookkeeper-view">
    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a(Yii::t('app', 'Изменить'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Audit::haveAuditOrNot($model->id, 'Bookkeeper') ? Html::a(Yii::t('titles', 'Show changelog'), ['/audit/default/index', 'parent_id' => $model->id, 'classname' => 'Bookkeeper'], ['class' => 'btn btn-info']) : '' ?>
        <?= Html::a(Yii::t('app', 'Удалить'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger pull-right',
            'data' => [
                'confirm' => Yii::t('app', 'Вы действитьльно хотите удалить?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'tl_delivery_proposal_id',
//            'tl_delivery_proposal_route_unforeseen_expenses_id',
            [
                'attribute' => 'tl_delivery_proposal_id',
                'format' => 'raw',
                'value' => $model->showDp($storeArray),
            ],
            [
                'attribute' => 'department_id',
                'value' => $model->getDepartmentIdValue(),
            ],
            [
                'attribute' => 'doc_type_id',
                'value' => $model->getDocTypeIdValue(),
            ],
            [
                'attribute' => 'type_id',
                'value' => $model->getTypeValue(),
            ],
            [
                'attribute' => 'expenses_type_id',
                'value' => $model->getExpensesTypeIdValue(),
            ],
            [
                'attribute' => 'cash_type',
                'value' => $model->getCashTypeValue(),
            ],
            'name_supplier',
            'description',
            [
                'label' => $model->getTypeValue(),
                'attribute' => 'price',
                'value' => $model->price,
            ],
            'balance_sum',

            [
                'attribute' => 'status',
                'value' => $model->getStatusValue(),
            ],
            'date_at:date',
            [
                'attribute' => 'created_user_id',
                'value' => $model::getUserName($model->created_user_id),
            ],
            [
                'attribute' => 'updated_user_id',
                'value' => $model::getUserName($model->updated_user_id),
            ],
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>
</div>