<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 12.11.2019
 * Time: 11:31
 */
?>
<h1>Отсканировали в лист отгрузки</h1>
<table class="table table-bordered">
    <thead>
        <td>#</td>
        <td>Шк места</td>
        <td>Курьерская компания</td>
        <td>номер заказ</td>
        <td>Defacto TTN</td>
        <td>Номер листа</td>
    </thead>
<?php $totalQty = count($orderInList); ?>
<?php foreach($orderInList as $key=>$productRow)  { ?>
    <tr class="alert-success">
        <td><?= $totalQty-$key; ?></td>
        <td><?= $productRow['package_barcode']; ?></td>
        <td><?= $productRow['courier_company']; ?></td>
        <td><?= $productRow['client_order_number']; ?></td>
        <td><?= $productRow['ttn_delivery_company']; ?></td>
        <td><?= $productRow['list_title']; ?></td>
    </tr>
<?php } ?>
</table>
