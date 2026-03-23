<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 25.11.2019
 * Time: 12:52
 */
?>

<table class="table table-striped table-bordered">
   <tr align="left" valign="middle" >
      <th width="20%" align="left" valign="middle" border="1"><strong><?= Yii::t('outbound/forms', 'Secondary address') ?></strong></th>
      <th width="25%" align="left" valign="middle" border="1"><strong><?= Yii::t('outbound/forms', 'Primary address') ?></strong></th>
      <th width="25%" align="left" valign="middle" border="1"><strong><?= Yii::t('outbound/forms', 'Product Barcode') ?></strong></th>
      <th width="5%" align="left" valign="middle" border="1"><strong><?= Yii::t('outbound/forms', 'Кол-во') ?></strong></th>
      <th width="30%" align="left" valign="middle" border="1"><strong><?= Yii::t('outbound/forms', 'Причина') ?></strong></th>
      <th width="25%" align="left" valign="middle" border="1"><strong><?= Yii::t('outbound/forms', 'Заменить') ?></strong></th>
   </tr>

<?php foreach ($reservedProducts as $productOnStock) {
    $pickingReasonList = \yii\bootstrap\Html::dropDownList('change_reason','',\common\ecommerce\constants\OutboundCancelStatus::getForPartReReservedList(),[
        'id'=>'change-reason-'. $productOnStock['id'],
        'class'=>'form-control',
        'prompt'=>'Выберите причину',
    ]);
?>

<tr align="left" valign="middle" class="picking-stock" id="picking-stock-id-<?= $productOnStock['id'] ?>">
    <td align="left" valign="middle" border="1"><?= $productOnStock['place_address_barcode']?></td>
    <td align="left" valign="middle" border="1"><?= $productOnStock['box_address_barcode']?></td>
    <td align="left" valign="middle" border="1"><?= $productOnStock['product_barcode']?></td>
    <td align="left" valign="middle" border="1">1</td>
    <td align="left" valign="middle" border="1"><?= $pickingReasonList ?></td>
    <td align="left" valign="middle" border="1"><a href="#" data-product-barcode="<?= $productOnStock['product_barcode']?>" data-stock-id="<?= $productOnStock['id']?>"  data-url="<?= \yii\helpers\Url::toRoute(['show-other-product-addresses']) ?>" class="btn btn-primary show-other-place-bt" >Найти замену</a></td>
</tr>
<?php } ?>