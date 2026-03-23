<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 30.09.2017
 * Time: 15:08
 */
?>

<div id="inbound-items" class="table-responsive">
    <table class="table">
        <tr>
            <th><?= Yii::t('inbound/forms', 'ШК LC'); ?></th>
            <th><?= Yii::t('inbound/forms', 'ШК лота'); ?></th>
            <th><?= Yii::t('inbound/forms', 'ШК товара'); ?></th>
            <th><?= Yii::t('inbound/forms', 'Expected Qty'); ?></th>
            <th><?= Yii::t('inbound/forms', 'Accepted Qty'); ?></th>
        </tr>
        <tbody id="inbound-item-body">
        <?php foreach($items as $item) { ?>
            <tr style="background-color:<?php echo ($item->product_expected_qty == $item->product_accepted_qty ? '#57ff57' : 'lightgray') ?>">
                <th><?= $item->client_box_barcode; ?></th>
                <th><?= $item->lot_barcode; ?></th>
                <th><?= $item->product_barcode; ?></th>
                <th><?= $item->product_expected_qty; ?></th>
                <th><?= $item->product_accepted_qty; ?></th>
            </tr>
        <?php } ?>

        </tbody>
    </table>
</div>
