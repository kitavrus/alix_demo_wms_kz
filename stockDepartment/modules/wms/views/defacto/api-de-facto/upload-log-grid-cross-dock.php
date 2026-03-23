<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 01.04.15
 * Time: 17:45
 */

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;
use yii\helpers\Url;
use common\modules\store\models\Store;
use common\modules\stock\models\Stock;
use common\helpers\iHelper;
?>
<?= \yii\bootstrap\Alert::widget([
    'options' => [
        'id' => 'alert-message-cross-dock',
        'class' => 'alert-danger',
    ],
    'body' =>
        '<h3>'
        .$messages. ( $updateStatus ? Html::tag('span', Yii::t('other/api-de-facto/buttons',!empty($messages) ? 'Upload' :
                'Confirm upload').'<span id ="show-status-message"></span>', [
            'data'=>[
                'client-id'=>$client_id,
                'unique-key'=>$unique_key,
                'url'=>Url::toRoute('cross-dock-confirm')
            ],
            'class' => 'btn btn-danger btn-lg',
            'style' => ' margin:5px;',
            'id' => 'yes-upload-cross-dock-data-bt']) : "" )
        .'</h3>'
    ,
]);
?>

<?php if(!empty($dataProvider)) { ?>
    <?= \yii\grid\GridView::widget([
        'id' => 'grid-view-cross-dock-order-items',
        'dataProvider' => $dataProvider,
        'layout'=>'{items}',
        'pager'=>false,
        'sorter'=>false,
        'columns' => [
            'party_number',
            'order_number',
            'to_point_title',
            [
                'attribute'=>  'to_point_id',
                'value'=>function ($model) {
                    $storeTitle = '-МАГАЗИН НЕ НАЙДЕН-';
                    if($store = Store::findOne($model->to_point_id)) {
                        $storeTitle = Store::getPointTitle($store->id);
                    }
                    return $storeTitle;
                }
            ],
            'box_barcode',
            'box_m3',
            'weight_brut',
            'weight_net',
        ],
    ]); ?>
<?php } ?>