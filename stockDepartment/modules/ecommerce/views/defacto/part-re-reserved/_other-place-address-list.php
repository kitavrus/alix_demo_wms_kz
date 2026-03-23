<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 25.11.2019
 * Time: 12:52
 */
?>
<h1>Что заменяем: <?= \yii\helpers\ArrayHelper::getValue($stock,'place_address_barcode').' / '.\yii\helpers\ArrayHelper::getValue($stock,'box_address_barcode').' / '.\yii\helpers\ArrayHelper::getValue($stock,'product_barcode') ?></h1>

<?php if (empty($freeProducts)) { ?>
   <h1 align="center"><?= 'Нет аналогов<br /> Продолжайте сканировать заказ без этого товара' ?></h1>
<?php } else { ?>
    <table class="table table-striped table-bordered">
        <tr align="left" valign="middle" >
        <th width="25%" align="left" valign="middle" border="1"><strong><?= Yii::t('outbound/forms', 'Secondary address'); ?></strong></th>
        <th width="25%" align="left" valign="middle" border="1"><strong><?= Yii::t('outbound/forms', 'Primary address'); ?></strong></th>
        <th width="25%" align="left" valign="middle" border="1"><strong><?= Yii::t('outbound/forms', 'Product Barcode'); ?></strong></th>
        <th width="25%" align="left" valign="middle" border="1"><strong><?= Yii::t('outbound/forms', 'Кол-во'); ?></strong></th>
        <th width="25%" align="left" valign="middle" border="1"><strong><?= Yii::t('outbound/forms', 'Заменить'); ?></strong></th>
    </tr>

    <?php foreach ($freeProducts as $productOnStock) { ?>
    <tr align="left" valign="middle" class="other-place-stock" id="other-place-stock-id-'<?= $productOnStock['id'] ?>">
        <td align="left" valign="middle" border="1"><?= $productOnStock['place_address_barcode'] ?></td>
        <td align="left" valign="middle" border="1"><?= $productOnStock['box_address_barcode'] ?></td>
        <td align="left" valign="middle" border="1"><?= $productOnStock['product_barcode'] ?></td>
        <td align="left" valign="middle" border="1"> 1 </td>
        <td align="left" valign="middle" border="1"><a href="#" data-url="<?= \yii\helpers\Url::toRoute(['re-reserved', 'newStockId' => $productOnStock['id'], 'oldStockId' => $stock->id, 'changeReason' => $changeReason]) ?>" class="btn btn-warning re-reserved-bt" >Заменить</a></td>
    </tr>
    <?php } ?>
<?php } ?>