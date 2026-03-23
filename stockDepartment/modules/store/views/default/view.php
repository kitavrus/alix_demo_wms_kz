<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\modules\audit\models\Audit;

/* @var $this yii\web\View */
/* @var $model common\modules\store\models\Store */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('titles', 'Stores'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="store-view">

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
        <?= Audit::haveAuditOrNot($model->id, 'Store') ? Html::a(Yii::t('titles', 'Show changelog'), ['/audit/default/index', 'parent_id' => $model->id, 'classname' => 'Store'], ['class' => 'btn btn-info']) : '' ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
//            'id',
            'name',
            'legal_point_name',
            'shopping_center_name',
            'shopping_center_name_lat',
            'client.title',
//            'contact_first_name',
//            'contact_middle_name',
//            'contact_last_name',
//            'contact_first_name2',
//            'contact_middle_name2',
//            'contact_last_name2',
            'internal_code',
            'email:email',
            'phone',
            'phone_mobile',
            'title',
            'description:ntext',
            'address_type',
            [
                'attribute' => 'status',
                'value' => $model->getStatus()
            ],

            'country.name',
            'region.name',
            'city.name',
            'zip_code',
            'street',
            'house',

            'comment:ntext',
            'shop_code',
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
            'city_prefix',
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

</div>
