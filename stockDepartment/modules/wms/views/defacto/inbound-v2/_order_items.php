<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 14.01.15
 * Time: 11:53
 */
?>

<?php foreach($items as $item) { ?>
    <?= '<tr id="row-'.$item['id'].'-'.$item['product_barcode'].'" class="'.($item['accepted_qty'] == $item['expected_qty'] ? 'alert-success' : ($item['accepted_qty'] > $item['expected_qty'] ? 'alert-warning' : 'alert-danger')).'">';?>
        <?= '<td>'.$item['product_barcode'].'</td>'; ?>
        <?= '<td>'.$item['product_model'].'</td>'; ?>
        <?= '<td>'.$item['expected_qty'].'</td>'; ?>
        <?= '<td id="accepted-qty-'.$item['id'].'-'.$item['product_barcode'].'">'.$item['accepted_qty'].'</td>'; ?>
        <?= '<td>'.$item['box_barcode'].'</td>'; ?>
        <?= '<td>'. (isset($item['expected_number_places_qty']) && isset($item['expected_number_places_qty']) && !empty($item['expected_number_places_qty']) ? ($item['expected_qty'] / $item['expected_number_places_qty']) : '-').'</td>'; ?>
    <?= '</tr>'; ?>
<?php } ?>