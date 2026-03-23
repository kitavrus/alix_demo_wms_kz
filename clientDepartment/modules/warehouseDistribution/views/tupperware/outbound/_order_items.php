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
        'id' => 'tupperware-confirm-upload-btn',
    ])
    ?>
    <?= Html::tag('span', Yii::t('outbound/buttons', 'Upload other file'),
        [
            'data-url' => Url::toRoute('upload-outbound-order'),
            'class' => 'btn btn-warning',
            'id' => 'tupperware-reset-upload-bt',
        ])
    ?>
  <span style="margin-left: 300px"> Всего товаров: <span id="sum-order" class="label label-primary"><?= $itemsQty ?></span></span> </h1>
    <br/>
    <div id="outbound-items" class="table-responsive">
        <table class="table">
            <tr>
<!--                <th>--><?php //echo Yii::t('stock/forms', 'Brand')?><!--</th>-->
<!--                <th>--><?php //echo Yii::t('stock/forms', 'Category')?><!--</th>-->
<!--                <th>--><?php //echo Yii::t('stock/forms', 'Stock code')?><!--</th>-->
<!--                <th>--><?php //echo Yii::t('stock/forms', 'SKU')?><!--</th>-->
<!--                <th>--><?php //echo Yii::t('stock/forms', 'Product model')?><!--</th>-->
<!--                <th>--><?php //echo Yii::t('stock/forms', 'Color')?><!--</th>-->
<!--                <th>--><?php //echo Yii::t('stock/forms', 'Size')?><!--</th>-->
<!--                <th>--><?php //echo Yii::t('stock/forms', 'Kavala')?><!--</th>-->
                <th><?= Yii::t('stock/forms', 'Product qty')?></th>
                <th><?= Yii::t('stock/forms', 'Product barcode')?></th>
                <th><?= Yii::t('stock/forms', 'Название')?></th>
            </tr>

            <tbody id="outbound-item-body">
            <?php foreach($previewData as $item) { ?>
                    <tr>
<!--                        <td>--><?php //echo $item['brand'] ?><!--</td>-->
<!--                        <td>--><?php //echo$item['category'] ?><!--</td>-->
<!--                        <td>--><?php //echo$item['internal_id'] ?><!--</td>-->
<!--                        <td>--><?php //echo$item['article'] ?><!--</td>-->
<!--                        <td>--><?php //echo$item['model'] ?><!--</td>-->
<!--                        <td>--><?php //echo$item['color'] ?><!--</td>-->
<!--                        <td>--><?php //echo$item['size'] ?><!--</td>-->
<!--                        <td>--><?php //echo$item['kavala'] ?><!--</td>-->
                        <td><?=$item['qty'] ?></td>
                        <td><?=$item['product_barcode'] ?></td>
                        <td><?=$item['product_name'] ?></td>
                    </tr>
            <?php }?>
            </tbody>
        </table>
    </div>

<?php }?>

