<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 14.01.15
 * Time: 11:53
 */
?>
<table class="table">
    <tr>
        <th><?= Yii::t('outbound/forms', 'Шк нашего короба'); ?></th>
        <th><?= Yii::t('outbound/forms', 'Шк короба клиента'); ?></th>
        <th><?= Yii::t('outbound/forms', 'Шк товара'); ?></th>
        <th><?= Yii::t('outbound/forms', 'Кол-во'); ?></th>
    </tr>
    <tbody id="outbound-item-body">
    <?php if (!empty($items)) { ?>
        <?php foreach ($items as $item) { ?>
            <?= '<tr class="' . ($item['transfer_outbound_box'] ? 'alert-success' : 'alert-danger') . '">'; ?>
            <?= '<td >' . $item['box_address_barcode']  . '</td>'; ?>
            <?= '<td >' . $item['transfer_outbound_box']  . '</td>'; ?>
            <?= '<td >' . $item['product_barcode']  . '</td>'; ?>
            <?= '<td >' . $item['productQty']  . '</td>'; ?>
            <?= '</tr>'; ?>
        <?php } ?>
    <?php } ?>
    </tbody>
</table>
