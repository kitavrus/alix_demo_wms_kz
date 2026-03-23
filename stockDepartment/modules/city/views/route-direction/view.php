<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\modules\city\models\RouteDirections */

$this->title = $routeDirectionModel->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Направления'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="route-directions-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Изменить'), ['update', 'id' => $routeDirectionModel->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Удалить'), ['delete', 'id' => $routeDirectionModel->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Вы действительно хотите удалить эту запись?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $routeDirectionModel,
        'attributes' => [
//            'id',
            'name',
            [
                'attribute' => 'base_type',
                'value' => $routeDirectionModel->getValueBaseType(),
            ],
            [
                'attribute' => 'created_user_id',
                'value' => $routeDirectionModel::getUserName($routeDirectionModel->created_user_id),
            ],
            [
                'attribute' => 'updated_user_id',
                'value' => $routeDirectionModel::getUserName($routeDirectionModel->updated_user_id),
            ],
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>
</div>
<?php  echo $this->render('_route_direction_search', [
    'model' => $rdCitySearchModel,
    'routeDirectionModel' => $routeDirectionModel,
    'cityArray' => $cityArray,
    'regionArray' => $regionArray,
    'countryArray' => $countryArray,
]); ?>
<?= GridView::widget([
    'dataProvider' => $cityProvider,
    'id' => 'route-direction-grid',
    'columns' => [
        [
            'class' => 'yii\grid\CheckboxColumn',
            'checkboxOptions' => function ($model, $key, $index, $column) use ($cityLinked) {
                return [
                    'value' => $model['cityId'],
                    'checked'=>in_array($model['cityId'],$cityLinked),
                    'class'=>'checkbox-column-item'
                ];
            }
        ],
//        [
//            'attribute'=>'cityID',
//            'format'=>'raw',
//            'value'=>function($data) {
//                return Html::checkbox('city-id[]');
//            }
//        ],
        [
            'attribute'=>'cityName',
            'label'=>'Город / Область / Страна',
            'format'=>'raw',
            'value'=>function($data) {
                return '<strong>'.$data['cityName'].'</strong> / '.$data['regionName'].' / '.$data['countryName'];
            }
        ]
    ],
]); ?>

<script type="text/javascript">
    $(function() {
        $('.checkbox-column-item').on('click', function () {
            var id = $(this).val();
//            var checked = $(this).is(':checked');
            var checked = $(this).prop('checked');
            console.info(id);
            console.info(checked);

            $.post('link-city', {id: id, checked:checked,rdID:<?php echo $routeDirectionModel->id; ?>}, function () {
                //alert('Уведомление успешно отправлено');
            });
        });
    });
</script>