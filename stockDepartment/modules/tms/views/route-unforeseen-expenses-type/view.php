<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\modules\transportLogistics\models\TlDeliveryProposalRouteUnforeseenExpensesType */

$this->title = Yii::t('transportLogistics/forms', 'create-route-unforeseen-expenses-type');
$this->params['breadcrumbs'][] = ['label' => Yii::t('titles', 'Route unforeseen expenses type'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tl-delivery-proposal-route-unforeseen-expenses-type-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('buttons', 'Edit'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            [
//                'displayOnly' => true,
//                'type' => DetailView::INPUT_DROPDOWN_LIST,
//                'items' => $model::getStatusArray(),
                'attribute' => 'status',
                'value' => $model->getStatusValue(),

            ],
            [
//                'displayOnly' => true,
                'attribute' => 'created_user_id',
                'value' => $model::getUserName($model->created_user_id),

            ],
            [
//                'displayOnly' => true,
                'attribute' => 'updated_user_id',
                'value' => $model::getUserName($model->updated_user_id),

            ],
            [
//                'displayOnly' => true,
                'attribute' => 'created_at',
                'format' => 'datetime'

            ],
            [
//                'displayOnly' => true,
                'attribute' => 'updated_at',
                'format' => 'datetime'

            ],
        ],
    ]) ?>

</div>
