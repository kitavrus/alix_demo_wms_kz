<?php
/**
 * Created by PhpStorm.
 * User: Kitavrus
 * Date: 05.01.2018
 * Time: 10:27
 */

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\clientObject\main\stock\models\StockRemainsSearch */
/* @var $activeDataProvider yii\data\ActiveDataProvider */
/* @var $clientsArray array */

$this->title = Yii::t('stock/titles', 'Поврежденные остатки на складе');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="search-item-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel,'clientsArray' => $clientsArray,]); ?>

    <?= GridView::widget([
        'dataProvider' => $activeDataProvider,
        'columns' => [
//            [
//                'label' => Yii::t('forms', 'Quantity'),
//                'attribute' => 'qty',
//            ],
            [
                'label' => Yii::t('stock/forms', 'Product barcode'),
                'attribute' => 'product_barcode',
            ],
            [
                'label' => Yii::t('stock/forms', 'Primary address'),
                'attribute' => 'primary_address',
            ],
            [
                'label' => Yii::t('stock/forms', 'Secondary address'),
                'attribute' => 'secondary_address',
            ],
            [
                'label' => Yii::t('stock/forms', 'Condition type'),
                'attribute' => 'condition_type',
                'value' => function($data) use ($conditionTypeArray){
                    return \common\overloads\ArrayHelper::getValue($conditionTypeArray,$data['condition_type']);
//                    return isset ($conditionTypeArray[$data['condition_type']]) ?$conditionTypeArray[$data['condition_type']] : '-';
                }
            ],
            [
                'attribute'=>'action',
                'label'=>'Действие',
                'format'=>'raw',
                'value'=>function($data) {
                    return \yii\bootstrap\Html::a("Доступен для резерва",\yii\helpers\Url::toRoute(['change-to-undamaged','stockId'=>$data['id']]),['class'=>'btn btn-danger', 'data-confirm' => Yii::t('yii', 'Вы действительно хотите восстановить товар из поврежденного')]);
                },
            ],
            [
                'attribute'=>'action',
                'label'=>'Фото',
                'format'=>'raw',
                'value'=>function($data) {
                    return \yii\bootstrap\Html::a("Добавить фото",\yii\helpers\Url::toRoute(['add-photo','id'=>$data['id']]),['class'=>'btn btn-success']);
                },
            ],
        ],
    ]); ?>

</div>

<!--<div>-->
<!--    --><?//= Html::tag('span',
//        Yii::t('transportLogistics/buttons','Экспорт в Exel краткий'),
//        ['class' => 'btn btn-success','id'=>'stock-remains-search-export-btn', 'data-url'=>\yii\helpers\Url::to('/stock/stock/remains-export-to-excel')]) ?>
<!--    --><?//= Html::tag('span',
//        Yii::t('transportLogistics/buttons','Экспорт в Exel подробный'),
//        ['class' => 'btn btn-warning','id'=>'stock-remains-search-export-detail-btn', 'data-url'=>yii\helpers\Url::to(['/stock/stock/remains-export-to-excel'])]) ?>
<!--    <br />-->
<!--</div>-->

<!--<script type="text/javascript">-->
<!--    $(function(){-->
<!---->
<!--        $('#stock-remains-search-export-btn,#stock-remains-search-export-detail-btn').on('click',function() {-->
<!--            var detail='';-->
<!--            if($(this).attr('id')=='stock-remains-search-export-detail-btn') {-->
<!--                detail = '&detail=y';-->
<!--            }-->
<!---->
<!--            window.location.href = $(this).data('url')+'?'+$('#stock-remains-search-form').serialize()+detail;-->
<!--        });-->
<!---->
<!--    });-->
<!--</script>-->