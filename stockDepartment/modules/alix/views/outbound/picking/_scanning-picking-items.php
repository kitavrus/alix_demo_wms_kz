<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 14.01.15
 * Time: 11:53
 */
?>
<?php //\yii\helpers\VarDumper::dump($items,10,true); die()?>
<table class="table">
    <tr>
        <th><?= Yii::t('outbound/forms', 'Product Barcode'); ?></th>
        <th><?= Yii::t('outbound/forms', 'Product Model'); ?></th>
        <th><?= Yii::t('outbound/forms', 'Box Barcode'); ?></th>
        <th><?= Yii::t('outbound/forms', 'Qty'); ?></th>
    </tr>
    <tbody id="outbound-item-body">
    <?php if (!empty($items)) { ?>
        <?php foreach ($items as $item) { ?>
            <?= '<tr id="row-' . $item['id'] . '-' . $item['product_barcode'] . '" class="' . (!empty($item['box_barcode']) ? 'alert-success' : 'alert-danger') . '">'; ?>
            <?= '<td>' . $item['product_barcode'] . '</td>'; ?>
            <?= '<td>' . $item['product_model'] . '</td>'; ?>
            <?= '<td id="box-barcode-' . $item['id'] . '-' . $item['product_barcode'] . '">' . $item['box_barcode'] . '</td>'; ?>
            <?= '<td id="accepted-qty-' . $item['id'] . '-' . $item['product_barcode'] . '">' . $item['items'] . '</td>'; ?>
            <?= '</tr>'; ?>
        <?php } ?>
    <?php } ?>
    </tbody>
</table>
