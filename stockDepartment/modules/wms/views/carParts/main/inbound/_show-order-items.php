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
            <th><?= Yii::t('inbound/forms', 'Название'); ?></th>
            <th><?= Yii::t('inbound/forms', 'Штрих-код'); ?></th>
            <th><?= Yii::t('inbound/forms', 'Артикул'); ?></th>
            <th><?= Yii::t('inbound/forms', 'Expected Qty'); ?></th>
            <th><?= Yii::t('inbound/forms', 'Accepted Qty'); ?></th>
        </tr>
        <tbody id="inbound-item-body">
        <?php foreach($items as $item) { ?>
            <tr style="background-color:<?php echo ($item->expected_qty == $item->accepted_qty ? '#57ff57' : 'lightgray') ?>">
                <th><?= $item->product_name; ?></th>
                <th><?= $item->product_barcode; ?></th>
                <th><?= $item->product_model; ?></th>
                <th><?= $item->expected_qty; ?></th>
                <th><?= $item->accepted_qty; ?></th>
            </tr>
        <?php } ?>

        </tbody>
    </table>
</div>
