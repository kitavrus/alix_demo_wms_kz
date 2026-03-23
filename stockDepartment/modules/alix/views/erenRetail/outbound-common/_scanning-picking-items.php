<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 14.01.15
 * Time: 11:53
 */
?>
<?php //\yii\helpers\VarDumper::dump($items,10,true); die()?>

<?php if( !empty($items) ) { ?>
    <?php foreach($items as $item) { ?>
        <?= '<tr id="row-'.$item['id'].'-'.$item['product_barcode'].'" class="'.(!empty($item['box_barcode'])  ? 'alert-success'  : 'alert-danger') . '">';?>
<!--        --><?//= '<tr id="row-'.$item['id'].'-'.$item['product_barcode'].'" class="'.($item['items'] == $item['items'] ? 'alert-success' : ($item['items'] > $item['items'] ? 'alert-warning' : 'alert-danger')).'">';?>
            <?= '<td>'.$item['product_barcode'].'</td>'; ?>
            <?= '<td>'.$item['product_model'].'</td>'; ?>
            <?= '<td id="box-barcode-'.$item['id'].'-'.$item['product_barcode'].'">'.$item['box_barcode'].'</td>'; ?>
<!--            --><?//= '<td>'.$item['count_status_picked'].'</td>'; ?>
<!--            --><?//= '<td>'.$item['count_status_sorting'].'</td>'; ?>
<!--            --><?//= '<td>'.$item['count_status_sorted'].'</td>'; ?>
<!--            --><?//= '<td>'.$item['count_exp'].'</td>'; ?>
            <?= '<td id="accepted-qty-'.$item['id'].'-'.$item['product_barcode'].'">'.$item['items'].'</td>'; ?>
        <?= '</tr>'; ?>
    <?php } ?>
<?php } ?>