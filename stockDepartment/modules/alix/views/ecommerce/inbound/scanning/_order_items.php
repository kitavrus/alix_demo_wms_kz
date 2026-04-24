<?php
/* @var $items common\ecommerce\entities\EcommerceInboundItem[] */
?>

<?php foreach ($items as $item) { ?>
    <?= '<tr id="row-' . $item['id'] . '-' . $item['product_barcode'] . '" class="' . ($item['product_accepted_qty'] == $item['product_expected_qty'] ? 'alert-success' : ($item['product_accepted_qty'] > 0 ? 'alert-warning' : 'alert-danger')) . '">'; ?>
    <?= '<td>' . $item['product_barcode'] . '</td>'; ?>
    <?= '<td>' . $item['product_model'] . '</td>'; ?>
    <?= '<td>' . $item['product_expected_qty'] . '</td>'; ?>
    <?= '<td id="accepted-qty-' . $item['id'] . '-' . $item['product_barcode'] . '">' . $item['product_accepted_qty'] . '</td>'; ?>
    <?= '</tr>'; ?>
<?php } ?>
