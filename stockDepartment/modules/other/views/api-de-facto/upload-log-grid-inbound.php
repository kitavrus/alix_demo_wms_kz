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
        'id' => 'alert-message-inbound',
        'class' => 'alert-danger',
    ],
    'body' =>
        '<h3>'
        .$messages. ( $updateStatus ? Html::tag('span', Yii::t('other/api-de-facto/buttons',!empty($messages) ? 'Upload' : 'Confirm upload').'<span id ="show-status-message"></span>', ['data'=>['client-id'=>$client_id,'unique-key'=>$unique_key], 'class' => 'btn btn-danger btn-lg', 'style' => ' margin:5px;', 'id' => 'yes-upload-inbound-data-bt']) : "" )
        .'</h3>'
    ,
]);
?>
<?php if(!empty($dataProvider)) { ?>
    <?= \yii\grid\GridView::widget([
        'id' => 'grid-view-inbound-order-items',
        'dataProvider' => $dataProvider,
        'layout'=>'{items}',
        'pager'=>false,
        'sorter'=>false,
        'columns' => [
            'order_number',
            'product_barcode',
            'product_model',
            'expected_qty',
        ],
    ]); ?>
<?php } ?>