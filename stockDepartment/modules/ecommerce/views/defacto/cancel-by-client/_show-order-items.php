<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 12.12.2019
 * Time: 13:46
 */
?>
<h3>Что в заказе:  <?= $dto->outboundOrderNumber; ?></h3>
<table class="table table-bordered">
    <thead>
    <td>#</td>
    <td>Группа отмены</td>
    <td>Номер заказа</td>
    <td>Шк короба отгрузки</td>
    <td>Шк товара</td>
    <td>Шк нового короба</td>
    </thead>
    <?php $totalQty = count($items); ?>
    <?php foreach($items as $key=>$productRow)  { ?>
        <tr class="<?= empty($productRow->newBoxAddress) ? 'alert-danger' : 'alert-success'?>">
            <td><?= $totalQty-$key; ?></td>
            <td><?= $productRow->cancelKey; ?></td>
            <td><?= $productRow->orderNumber; ?></td>
            <td><?= $productRow->outboundBox; ?></td>
            <td><?= $productRow->productBarcode; ?></td>
            <td><?= $productRow->newBoxAddress; ?></td>
        </tr>
    <?php } ?>
</table>