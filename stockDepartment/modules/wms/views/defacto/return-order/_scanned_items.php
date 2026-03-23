<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 14.01.15
 * Time: 11:53
 */
?>
<?php if( !empty($items) ) { ?>
<table class="table">
        <tr>
            <th><?= Yii::t('return/forms', 'Product Barcode'); ?></th>
            <th><?= Yii::t('return/forms', 'Expected qty'); ?></th>
            <th><?= Yii::t('return/forms', 'Accepted qty'); ?></th>
        </tr>
        <tbody id="outbound-item-body">
            <?php foreach($items as $item) { ?>
                <?= '<tr id="row-'.$item['id'].'-'.$item['product_barcode'].'" class="'.($item['accepted_qty'] == $item['expected_qty'] ? 'alert-success' : ($item['accepted_qty'] > $item['expected_qty'] ? 'alert-warning' : 'alert-danger')).'">';?>
                    <?= '<td>'.$item['product_barcode'].'</td>'; ?>
                    <?= '<td>'.$item['expected_qty'].'</td>'; ?>
                    <?= '<td>'.$item['accepted_qty'].'</td>'; ?>
                <?= '</tr>'; ?>
            <?php } ?>
        </tbody>
</table>
<?php } ?>

