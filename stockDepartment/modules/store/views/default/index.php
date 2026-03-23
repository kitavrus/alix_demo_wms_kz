<?php

use yii\helpers\Html;
use common\modules\store\models\Store;
use kartik\grid\GridView;
use kartik\grid\EditableColumn;
use kartik\grid\DataColumn;

/* @var $this yii\web\View */
/* @var $searchModel stockDepartment\modules\store\models\StoreSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('titles', 'Stores');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="store-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('buttons', 'Create Store'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'floatHeader' => true,
        'id' => 'grid',
        'columns' => [
            [
                'class' => '\kartik\grid\CheckboxColumn'
            ],
            [

//                'class' => EditableColumn::className(),
//                'editableOptions' => [
//                    'inputType' => 'dropDownList',
//                    'data' =>$searchModel::getTypeUseArray(),
//                    'placement' => 'right',
//                ],
                'attribute' => 'type_use',
                'value' => function ($model) {
                    return $model::getTypeUseArray($model->type_use);
                },
                'filter' => $searchModel::getTypeUseArray(),
            ],


//            [
//                'class' => DataColumn::className(),
//                'name' => 'type_use',
//                'attribute' => 'type_use',
//                'value' => 'client.username',
//                'filterType' => GridView::FILTER_SELECT2,
//                'filterWidgetOptions' => [
//                    'data' => $searchModel->getTypeUseArray(),
//                    'options' => [
//                        'placeholder' => Yii::t('forms', 'Select use type'),
//                    ],

//                ],
//                'value' => function ($model) {
//                    return $model::getTypeUseArray($model->type_use);
//                },
//                'filter' => $searchModel::getTypeUseArray(),
//
//            ],
            [
                'class' => DataColumn::className(),
                'attribute' => 'client_id',
                'value' => 'client.title',
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => $searchModel->getClientArray(),
                    'options' => [
                        'placeholder' => Yii::t('forms', 'Select client'),
                    ],

                ],

            ],
            'name',
            'shopping_center_name',
            [
                'class' => DataColumn::className(),
                'attribute' => 'city_id',
                'value' => 'city.name',
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => $searchModel->getCityArray(),
                    'options' => [
                        'placeholder' => Yii::t('forms', 'Select city'),
                    ],
                ],

            ],
            'shop_code',
            'shop_code2',
            'city_prefix',
            'contact_first_name',
            'contact_last_name',
            [
                'class' => EditableColumn::className(),
                'editableOptions' => [
                    'inputType' => 'dropDownList',
                    'data' =>$searchModel::getStatusArray(),
                    'placement' => 'left',
                ],
                'attribute' => 'status',
                'value' => function ($model) {
                        return $model::getStatusArray($model->status);
                    },
                'filter' => $searchModel::getStatusArray(),
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

<div><?= Html::tag('span','Уведомить о получении',['class' => 'btn btn-success','id'=>'send-mail-ok-to-store-btn']) ?>
<?= Html::tag('span','Уведомить о отмене',['class' => 'btn btn-success','id'=>'send-mail-cancel-to-store-btn']) ?></div>
</div>

<script type="text/javascript">
    $(function(){
        $('#send-mail-ok-to-store-btn').on('click',function() {
            var keys = $('#grid').yiiGridView('getSelectedRows');
            console.info(keys);
            $.post('/store/default/send-email-to-store',{storeIds:keys,'type':'ok'},function(){
               alert('Уведомление успешно отправлено');
            });
        });

        $('#send-mail-cancel-to-store-btn').on('click',function() {
            var keys = $('#grid').yiiGridView('getSelectedRows');
            console.info(keys);
            $.post('/store/default/send-email-to-store',{storeIds:keys,'type':'cancel'},function(){
               alert('Уведомление успешно отправлено');
            });
        });
    });
</script>
