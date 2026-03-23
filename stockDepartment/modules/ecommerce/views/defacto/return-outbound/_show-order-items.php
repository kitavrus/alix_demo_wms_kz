<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 12.12.2019
 * Time: 13:46
 */
?>
<h1>Что в заказе:  <?= $dto->orderNumber; ?></h1>
<table class="table table-bordered">
    <thead>
    <td>#</td>
    <td>Шк товара</td>
    <td>Ожидали кол-во товара</td>
    <td>Приняли кол-во товара</td>
    </thead>
    <?php $totalQty = count($items); ?>
    <?php foreach($items as $key=>$productRow)  { ?>
        <tr class="<?= $productRow['expected_qty'] != $productRow['accepted_qty'] ? 'alert-danger' : 'alert-success'?>">
            <td><?= $totalQty-$key; ?></td>
            <td><?= $service->showNotEmptyProductBarcode($productRow); ?></td>
            <td><?= $productRow['expected_qty']; ?></td>
            <td><?= $productRow['accepted_qty']; ?></td>
        </tr>
    <?php } ?>
</table>
