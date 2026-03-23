<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>
<?php if(!empty($previewData) && is_array($previewData)) { ?>
    <h1>
    <?= Html::tag('span', Yii::t('outbound/buttons', 'Upload'),
        [
        'data-url' => Url::toRoute('create-outbound-order'),
        'class' => 'btn btn-danger',
            //'data-confirm-upload' => '1',
        'id' => 'koton-confirm-upload-btn',
    ])
    ?>
    <?= Html::tag('span', Yii::t('outbound/buttons', 'Upload other file'),
        [
            'data-url' => Url::toRoute('upload-outbound-order'),
            'class' => 'btn btn-warning',
            'id' => 'koton-reset-upload-bt',
        ])
    ?>
  <span style="margin-left: 300px"> Всего товаров: <span id="sum-order" class="label label-primary"><?= $itemsQty ?></span></span> </h1>
    <br/>
    <div id="outbound-items" class="table-responsive">
        <table class="table">
            <tr>
                <th><?= Yii::t('stock/forms', 'Brand')?></th>
                <th><?= Yii::t('stock/forms', 'Category')?></th>
                <th><?= Yii::t('stock/forms', 'Stock code')?></th>
                <th><?= Yii::t('stock/forms', 'SKU')?></th>
                <th><?= Yii::t('stock/forms', 'Product model')?></th>
                <th><?= Yii::t('stock/forms', 'Color')?></th>
                <th><?= Yii::t('stock/forms', 'Size')?></th>
                <th><?= Yii::t('stock/forms', 'Kavala')?></th>
                <th><?= Yii::t('stock/forms', 'Product qty')?></th>
                <th><?= Yii::t('stock/forms', 'Product barcode')?></th>
            </tr>

            <tbody id="outbound-item-body">
            <?php foreach($previewData as $item) { ?>
                    <tr>
                        <td><?=$item['brand'] ?></td>
                        <td><?=$item['category'] ?></td>
                        <td><?=$item['internal_id'] ?></td>
                        <td><?=$item['article'] ?></td>
                        <td><?=$item['model'] ?></td>
                        <td><?=$item['color'] ?></td>
                        <td><?=$item['size'] ?></td>
                        <td><?=$item['kavala'] ?></td>
                        <td><?=$item['qty'] ?></td>
                        <td><?=$item['product_barcode'] ?></td>
                    </tr>
            <?php }?>
            </tbody>
        </table>
    </div>

<?php }?>

