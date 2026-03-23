<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 14.01.15
 * Time: 11:53
 */
?>
<?php foreach($items as $item) { ?>
    <?php $accepted_qty = isset($acceptedQtyItems[$item['product_barcode']]) ? $acceptedQtyItems[$item['product_barcode']] : 0; ?>
    <?= '<tr id="row-'.$item['product_barcode'].'" class="'.($accepted_qty == $item['expected_qty'] ? 'alert-success' : ($accepted_qty > $item['expected_qty'] ? 'alert-warning' : 'alert-danger')).'">';?>
        <?= '<td>'.$item['product_barcode'].'</td>'; ?>
        <?= '<td>'.$item['product_model'].'</td>'; ?>
        <?= '<td>'.$item['expected_qty'].'</td>'; ?>
        <?= '<td id="accepted-qty-'.$item['product_barcode'].'">'.$accepted_qty.'</td>'; ?>
    <?= '</tr>'; ?>
<?php } ?>