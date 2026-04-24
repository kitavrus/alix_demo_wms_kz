<?php
/* @var $items array */
?>
<?php foreach ($items as $item) { ?>
    <?= '<tr id="row-' . $item['id'] . '-' . $item['product_barcode'] . '" class="' . ($item['accepted_qty'] == $item['expected_qty'] ? 'alert-success' : ($item['accepted_qty'] > 0 ? 'alert-warning' : 'alert-danger')) . '">'; ?>
    <?= '<td>' . $item['product_barcode'] . '</td>'; ?>
    <?= '<td>' . $item['expected_qty'] . '</td>'; ?>
    <?= '<td id="accepted-qty-' . $item['id'] . '-' . $item['product_barcode'] . '">' . $item['accepted_qty'] . '</td>'; ?>
    <?= '</tr>'; ?>
<?php } ?>
