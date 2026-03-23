<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 12.12.2019
 * Time: 13:45
 */
?>
<h1>Что в коробе: <?= $dto->lcBarcode; ?></h1>
<table class="table table-bordered">
    <thead>
        <td>#</td>
        <td>Шк товара</td>
        <td>Шк короба</td>
        <td>Кол-во товара</td>
    </thead>
<?php $totalQty = count($items); ?>
<?php foreach($items as $key=>$productRow)  { ?>
    <tr class="alert-success">
        <td><?= $totalQty-$key; ?></td>
        <td><?= $productRow['product_barcode']; ?></td>
        <td><?= $productRow['anyBox']; ?></td>
        <td><?= $productRow['qtyProduct']; ?></td>
    </tr>
<?php } ?>
</table>