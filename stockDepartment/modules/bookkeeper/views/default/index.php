<?php

use yii\helpers\Html;
//use yii\grid\GridView;
use kartik\grid\GridView;
use kartik\grid\EditableColumn;
use kartik\grid\DataColumn;
use stockDepartment\modules\bookkeeper\models\Bookkeeper;

/* @var $this yii\web\View */
/* @var $searchModel stockDepartment\modules\bookkeeper\models\BookkeeperSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = $balance;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bookkeeper-index">

    <h1><?= "<h1>"."Остаток: ".$balance."</h1>"; ?></h1>
    <?php  echo $this->render('_search', ['model' => $searchModel,'storeArray' => $storeArray]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Создать расход'), ['create','type'=>\common\modules\bookkeeper\models\Bookkeeper::TYPE_MINUS], ['class' => 'btn btn-success']) ?>
        <?= Html::a(Yii::t('app', 'Создать приход'), ['create','type'=>\common\modules\bookkeeper\models\Bookkeeper::TYPE_PLUS], ['class' => 'btn btn-warning']) ?>


        <?= Html::a(Yii::t('app', 'Полный пересчет'), ['full-recalculate'], ['id'=>'full-recalculate-bookkeeper-bt','class' => 'btn btn-danger pull-right']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'rowOptions'=> function ($model, $key, $index, $grid) {
            $class = Bookkeeper::getGridColorByValue($model['status']);
            return ['class'=>$class];
        },
        'columns' => [
			'id',
            [
                'class' => DataColumn::className(),
                'attribute' => 'date_at',
                'value' => function($data){
                    return $data->showDateAt();
                },
//                'filterType' => GridView::FILTER_DATE_RANGE,
//                'filterWidgetOptions' => [
//                    'convertFormat'=>true,
//                    'pluginOptions'=>[
//                        'locale'=>[
//                            'separator'=> ' / ',
//                            'format'=>'Y-m-d',
//                        ]
//                    ]
//                ],
            ],
            [
                'label' => 'Приход',
                'attribute' => 'price_plus',
                'value' => function ($model) {
                    return $model->showPlus();
                },
            ],
            'name_supplier',
            'description',
            [
                'attribute' => 'department_id',
                'value' => function ($model) {
                    return $model->getDepartmentIdValue();
                },
//                'filter'=> $searchModel->getDepartmentIdArray()
            ],
            [
                'label' => 'Расход',
                'attribute' => 'price_minus',
                'value' => function ($model) {
                    return $model->showMinus();
                },
            ],
            [
                'attribute' => 'balance_sum',
                'value' => function ($model) {
                    return $model->balance_sum;
                },
                'filter'=> false
            ],
            [
                'attribute' => 'status',
                'value' => function ($model) {
                    return $model->getStatusValue();
                },
//                'filter'=> $searchModel->getStatusArray()
            ],
            [
                'attribute' => 'doc_type_id',
                'value' => function ($model) {
                    return $model->getDocTypeIdValue();//.' '.$model->getCashTypeValue();
                },
//                'filter'=> $searchModel->getDocTypeIdArray()
            ],
            [
                'attribute' => 'tl_delivery_proposal_id',
                'format' => 'raw',
                'value' => function ($model) use ($storeArray) {
                    return $model->showDp($storeArray);
                },
                'filter'=> false
            ],
//            [
//                'attribute' => 'tl_delivery_proposal_route_unforeseen_expenses_id',
//                'value' => function ($model) {
//                    return $model->tl_delivery_proposal_route_unforeseen_expenses_id;
//                },
//                'filter'=> false
//            ],
            [
                'class' => 'yii\grid\ActionColumn',
            ],
        ],
    ]); ?>
</div>

<?= Html::tag('span',Yii::t('transportLogistics/buttons','Экспорт в Эксель'),['class' => 'btn btn-success','id'=>'bookkeeper-export-btn', 'data-url'=>'/bookkeeper/default/export-to-excel']) ?>
<?= Html::tag('span',Yii::t('transportLogistics/buttons','Закрыть расходы'),['class' => 'btn btn-danger pull-right','id'=>'bookkeeper-done-btn', 'data-url'=>'/bookkeeper/default/done']) ?>

<script type="text/javascript">
    $(function(){

        $('#bookkeeper-export-btn').on('click',function() {
            console.info($('#w0').find('.form-control').serialize());

            window.location.href = $(this).data('url')+'?'+$('#w0').find('.form-control').serialize();
        });

        $('#bookkeeper-done-btn').on('click',function() {
            if(confirm('Вы действительно хотите закрыть эти расходы')) {
                console.info($('#w0').find('.form-control').serialize());

                $(this).text('Пожалуйста подождите, выполняется');

                window.location.href = $(this).data('url') + '?' + $('#w0').find('.form-control').serialize();
            }
        });

        $('#full-recalculate-bookkeeper-bt').on('click',function(d) {

            console.info('full-recalculate-bookkeeper-bt');

            if(confirm('Вы действительно хотите сделать полный пересчет')) {

                var obj = $(this),
                    url = obj.attr('href');

                    obj.text('Пожалуйста подождите, выполняется');

                $.post(url, function (data) {

                    obj.text('Полный пересчет');
                    console.info(data);
                    alert('Полный пересчет успешно закончен');

                }, 'json');

            }
            return false;
        });
    });
</script>
