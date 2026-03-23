<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>
<?php if(!empty($previewData->expectedTotalProductQty)) { ?>
    <h1>
    <?= Html::tag('span', Yii::t('outbound/buttons', 'Upload'),
        [
        'data-url' => Url::toRoute('create-outbound-order'),
        'class' => 'btn btn-danger',
        'id' => 'eren-retail-confirm-upload-btn',
    ])
    ?>
    <?= Html::tag('span', Yii::t('outbound/buttons', 'Upload other file'),
        [
            'data-url' => Url::toRoute('outbound-order'),
            'class' => 'btn btn-warning',
            'id' => 'eren-retail-reset-upload-bt',
        ])
    ?>
  <span style="margin-left: 300px"> Всего товаров: <span id="sum-order" class="label label-primary"><?= $previewData->expectedTotalProductQty ?></span></span> </h1>
    <br/>
    <div id="outbound-items" class="table-responsive">
        <table class="table">
            <tr>
	            <th><?= Yii::t('stock/forms', 'Style')?></th>
	            <th><?= Yii::t('stock/forms', 'Color')?></th>
	            <th><?= Yii::t('stock/forms', 'Description')?></th>
	            <th><?= Yii::t('stock/forms', 'Ups')?></th>
	            <th><?= Yii::t('stock/forms', 'Size')?></th>
	            <th><?= Yii::t('stock/forms', 'Qty')?></th>
            </tr>

            <tbody id="outbound-item-body">
            <?php foreach($previewData->items as $item) { ?>
                    <tr>
                        <td><?=$item->productStyle ?></td>
                        <td><?=$item->productColor ?></td>
                        <td><?=$item->productName ?></td>
                        <td><?=$item->productBarcode ?></td>
                        <td><?=$item->productSize ?></td>
                        <td><?=$item->expectedProductQty ?></td>
                    </tr>
            <?php }?>
            </tbody>
        </table>
    </div>

<?php }?>

