<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 14.01.15
 * Time: 17:27
 */
//\yii\helpers\VarDumper::dump($items,10,true);
//echo "<br />-----<br />";
?>
<?php $this->title = Yii::t('inbound/titles', 'The list of differences');?>
<h1><?= \yii\helpers\Html::encode($this->title) ?></h1>
<div id="inbound-items" class="table-responsive">
    <table class="table">
        <tr>
            <th><?=Yii::t('inbound/forms','Product Barcode'); ?></th>
            <th><?=Yii::t('inbound/forms','Box Barcode'); ?></th>
            <th><?=Yii::t('inbound/forms','Product Model'); ?></th>
            <th><?=Yii::t('inbound/forms','Items'); ?></th>
        </tr>
        <tbody id="inbound-item-body">
        <?php foreach($items as $item) { ?>
             <tr>
                 <td><?= $item['product_barcode']; ?></td>
                 <td><?= $item['box_barcode']; ?></td>
                 <td><?= $item['product_model']; ?></td>
                 <td><?= $item['items']; ?></td>
             </tr>
            <?php //S: TODO Потом сделать это почеловечески
                    $itemsProcess = \common\modules\inbound\models\InboundOrderItemProcess::find()
                        ->select('id, product_barcode, box_barcode, product_model, count(*) as items ')
                        ->where([
                            'inbound_order_id'=>$item['inbound_order_id'],
                            'product_barcode'=>$item['product_barcode'],
                        ])
                        ->groupBy('product_barcode, box_barcode')
                        ->orderBy([
                            'box_barcode'=>SORT_DESC
                        ])
                        ->asArray()
                        ->all(); ?>

                <?php if($itemsProcess) { ?>
                    <?php foreach($itemsProcess as $value) { ?>
                        <tr>
                            <td><?= $value['product_barcode']; ?></td>
                            <td><?= $value['box_barcode']; ?></td>
                            <td><?= $value['product_model']; ?></td>
                            <td><?= $value['items']; ?></td>
                        </tr>
                    <?php } ?>
                <?php } //E: TODO Потом сделать это почеловечески ?>
        <?php } ?>
        </tbody>
    </table>
</div>