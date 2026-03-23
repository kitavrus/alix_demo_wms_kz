<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 12.11.2019
 * Time: 11:31
 */
?>

<table class="table table-bordered">
    <thead>
        <td>Статус</td>
        <td>ШК товара</td>
        <td>Количесво</td>
        <td>Номер Заказа</td>
        <td>Статус заказа</td>
        <td>Короб</td>
        <td>Место</td>
    </thead>
<?php foreach($productListInBox as $productRow)  { ?>
    <tr class="<?= (!empty($productRow['status']) ? 'alert-success' : 'alert-danger'); ?>">
        <td><?= \common\ecommerce\constants\CheckBoxStatus::getValue($productRow['status']); ?></td>
        <td><?= $productRow['product_barcode']; ?></td>
        <td><?= $productRow['qtyProduct']; ?></td>
        <td><?= $productRow['stock_outbound_id']; ?></td>
        <td><?= \common\ecommerce\constants\StockOutboundStatus::getValue($productRow['stock_outbound_status']); ?></td>
        <td><?= $productRow['box_barcode']; ?></td>
        <td><?= $productRow['place_address']; ?></td>
    </tr>
<?php } ?>
</table>
