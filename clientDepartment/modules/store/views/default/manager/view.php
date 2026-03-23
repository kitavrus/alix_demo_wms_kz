<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;

/* @var $this yii\web\View */
/* @var $model common\modules\store\models\Store */

$this->title = $model->name;
//$this->params['breadcrumbs'][] = ['label' => Yii::t('titles', 'Stores'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="store-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('buttons', 'Edit'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
<!--        --><?//= Html::a(Yii::t('buttons', 'Delete'), ['delete', 'id' => $model->id], [
//            'class' => 'btn btn-danger',
//            'data' => [
//                'confirm' => Yii::t('titles', 'Are you sure you want to delete this item?'),
//                'method' => 'post',
//            ],
//        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
//            'id',
            'name',
            'shopping_center_name',
            'shopping_center_name_lat',
            'shop_code',
//            'contact_first_name',
//            'contact_middle_name',
//            'contact_last_name',
//            'contact_first_name2',
//            'contact_middle_name2',
//            'contact_last_name2',
            'email:email',
            'phone',
            'phone_mobile',
//            'title',
            'description:ntext',
//            'address_type',
            [
                'attribute' => 'status',
                'value' => $model->getStatus()
            ],
            'country.name',
            'region.name',
            'city.name',
            'city_lat',
            'zip_code',
            'street',
            'house',

//            'comment:ntext',
//            'shop_code',
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

</div>

