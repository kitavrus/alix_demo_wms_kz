<?php
use yii\helpers\Html;
use kartik\grid\GridView;
//use common\modules\client\models\Client;

$this->title = Yii::t('client/titles', 'Clients');
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('forms/_client_search_form', ['searchModel'=>$searchModel]) ?>
<?= Html::endTag('br')?>
<?= Html::endTag('br')?>
<div class="clients">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'floatHeader' => true,
        'id' => 'client-grid',
        'columns' => [
            [
                'attribute'=> 'full_name',
                'format'=> 'html',
                'value' => function ($data) { return Html::tag('a', $data->full_name, ['href'=>\yii\helpers\Url::to(['view', 'id' => $data->id]), 'target'=>'_blank']);},
            ],
            'phone_mobile',
            'email',
        ],
    ]); ?>
</div>
<?= Html::a(Yii::t('frontend/buttons', 'Add order'), '/operatorDella/order/make-order', ['class' => 'btn btn-danger']) ?>