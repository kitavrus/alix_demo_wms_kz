<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 30.01.15
 * Time: 17:43
 */

use yii\helpers\Html;
use yii\helpers\Url;
use common\modules\store\models\Store;
use common\helpers\iHelper;
$this->title = Yii::t('outbound/titles', 'Report: ABC');
$this->params['breadcrumbs'][] = $this->title;
?>

<h1><?= Html::encode($this->title) ?></h1>
<?= $this->render('_search', ['model' => $searchModel, 'clientsArray' => $clientsArray]); ?>

<?= \yii\grid\GridView::widget([
//    'id' => 'grid-view-order-items',
    'dataProvider' => $activeDataProvider,
//    'rowOptions'=> function ($model, $key, $index, $grid) {
//        $class = iHelper::getStockGridColor($model->status);
//        return ['class'=>$class];
//    },
    'columns' => [
        [
            'attribute'=>'client_id',
            'value'=>function($data)use ($clientsArray) {
                return \common\overloads\ArrayHelper::getValue($clientsArray,$data['client_id']);
            },
        ],
         'product_barcode',
         'productQty',
    ],
]); ?>

<div>
    <?= Html::tag('span',Yii::t('transportLogistics/buttons','Export to Excel'),['class' => 'btn btn-success','id'=>'abc-export-btn', 'data-url'=>'to-excel']) ?>
    <br />
</div>

<script type="text/javascript">
    $(function() {
        $('#abc-export-btn').on('click',function() {
            window.location.href = $(this).data('url')+'?'+$('#outbound-orders-grid-search-form').serialize();
        });
    });
</script>