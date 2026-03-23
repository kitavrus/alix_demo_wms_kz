<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 12.11.2019
 * Time: 11:31
 */
use yii\bootstrap\Html;
//use common\ecommerce\constants\StockOutboundStatus;
//use common\ecommerce\constants\StockTransferStatus;
?>
<table class="table table-bordered">
    <thead>
        <td>Статус</td>
        <td>ШК товара</td>
        <td>Количесво</td>
        <td>Короб</td>
        <td>Место</td>
<!--        <td>Номер Заказа</td>-->
<!--        <td>Статус заказа</td>-->
<!--        <td>Трансфер</td>-->
<!--        <td>Трансфер статус</td>-->
    </thead>
<?php foreach($productListInBox as $productRow)  { ?>
    <tr class="<?= \common\b2b\domains\checkBox\constants\CheckBoxStatus::getCssClass($productRow['status']); ?>">
        <td><?= \common\b2b\domains\checkBox\constants\CheckBoxStatus::getValue($productRow['status']); ?></td>
        <td><?= $productRow['product_barcode']; ?></td>
        <td><?= $productRow['qtyProduct']; ?></td>
        <td><?= $productRow['box_barcode']; ?></td>
        <td><?= $productRow['place_address']; ?></td>

<!--        <td class="--><?//= (!empty($productRow['stock_outbound_id']) ? 'alert-warning' : ''); ?><!--">--><?//= Html::tag('a',$productRow['stock_outbound_id'], ['href'=>\yii\helpers\Url::to(['/ecommerce/defacto/report/outbound-view', 'id' => $productRow['stock_outbound_id']]), 'target'=>'_blank']) ?><!--</td>-->
<!--        <td class="--><?//= (!empty($productRow['stock_outbound_id']) ? 'alert-warning' : ''); ?><!--">--><?//= StockOutboundStatus::getValue($productRow['stock_outbound_status']); ?><!--</td>-->

<!--        <td class="--><?//= (!empty($productRow['stock_transfer_id']) ? 'alert-warning' : ''); ?><!--">--><?//= Html::tag('a',$productRow['stock_transfer_id'], ['href'=>\yii\helpers\Url::to(['/ecommerce/defacto/transfer-report/view', 'id' => $productRow['stock_transfer_id']]), 'target'=>'_blank']) ?><!--</td>-->
<!--        <td class="--><?//= (!empty($productRow['stock_transfer_id']) ? 'alert-warning' : ''); ?><!--">--><?//= StockTransferStatus::getValue($productRow['stock_status_transfer']); ?><!--</td>-->
    </tr>
<?php } ?>
</table>