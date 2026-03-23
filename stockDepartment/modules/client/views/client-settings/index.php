<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\client\models\ClientSettingsClientSettingsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('titles', 'Client Settings');
$this->params['breadcrumbs'][] = $this->title;
?>
<?= Html::a(Yii::t('titles', '<- Back to client page'), ['/client/default/view','id'=>$client]) ?>
<div class="client-settings-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('buttons', 'Create Client Setting'), ['create','client_id'=>$client], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

//            'id',
//            'client_id',
            'option_name',
            'option_value',
            //'default_value',
        [
        'attribute'=> 'default_value',
        'value' => function ($data) {
              $value = call_user_func('app\modules\client\components\ClientSettingsManager::'. $data->option_value);
              return isset($value[$data->default_value])? $value[$data->default_value] : '';
            },
        ],
            [
                'attribute'=> 'option_type',
                'value' => function ($data) { return $data->getOptionType($data->option_type);},
            ],
            'description:ntext',
//            'createdUser.username',
//            'updatedUser.username',
//            'created_at:datetime',
//            'updated_at:datetime',

            ['class' => 'yii\grid\ActionColumn', 'template' => '{view} {update}'],
        ],
    ]); ?>

</div>
