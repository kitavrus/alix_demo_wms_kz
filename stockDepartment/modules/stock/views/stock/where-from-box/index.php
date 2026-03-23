<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\stock\models\StockSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $clientStoreArray array */

$this->title = Yii::t('forms', 'Stocks');
$this->params['breadcrumbs'][] = $this->title;
?>
<?php  echo $this->render('_search', ['model' => $searchModel]); ?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
//    'filterModel' => $searchModel,
    'columns' => [
//        ['class' => 'yii\grid\SerialColumn'],
        'product_barcode',
        [
            'label' => Yii::t('outbound/forms', 'Кол-во'),
            'attribute'=>  'product_barcode_count',
        ],

        [
            'label' => Yii::t('outbound/forms', 'Заказ'),
            'attribute'=>  'outbound_picking_list_barcode',
        ],
        [
            'label' => Yii::t('outbound/forms', 'Магазин'),
            'value'=>function ($model) use ($clientStoreArray) {
                $outbound = \common\modules\outbound\models\OutboundOrder::findOne($model['outbound_order_id']);
                if($outbound) {
                    return \yii\helpers\ArrayHelper::getValue($clientStoreArray,$outbound->to_point_id);
                }
                return '';
            }
        ],

         'primary_address',
         'secondary_address',
    ],
]); ?>