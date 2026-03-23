<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 14.01.15
 * Time: 11:53
 */
?>
<?php if( !empty($items) ) { ?>
    <?php foreach($items as $item) { ?>
        <?= '<tr id="row-'.$item['id'].'-'.$item['product_barcode'].'" class="'.($item['accepted_qty'] == $item['expected_qty'] ? 'alert-success' : ($item['accepted_qty'] > $item['expected_qty'] ? 'alert-warning' : 'alert-danger')).'">';?>
            <?= '<td>'.$item['product_barcode'].'</td>'; ?>
            <?= '<td>'.$item['product_model'].'</td>'; ?>
            <?= '<td>'.$item['expected_qty'].'</td>'; ?>
            <?= '<td>'.$item['accepted_qty'].'</td>'; ?>
        <?= '</tr>'; ?>
    <?php } ?>
<?php } ?>