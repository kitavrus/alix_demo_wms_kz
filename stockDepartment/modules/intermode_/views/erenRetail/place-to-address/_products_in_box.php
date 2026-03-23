<?php foreach($productsInBox as $productRow)  { ?>
    <tr class="alert-success">
        <td><?= $productRow['product_barcode']; ?></td>
        <td><?= $productRow['qtyProduct']; ?></td>
    </tr>
<?php } ?>
