<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 08.10.2015
 * Time: 14:28
 */
use common\modules\crossDock\models\CrossDockItems;
use common\modules\crossDock\models\CrossDock;
use common\modules\store\models\Store;
use common\modules\crossDock\models\CrossDockItemProducts;
?>
<div>
    <table class="kv-grid-table table table-bordered table-striped kv-table-wrap">
        <tr>
            <th><?php echo 'Магазин'; ?></th>
            <th><?php echo 'Штрих код короба'; ?></th>
            <th><?php echo 'Штрих код товара'; ?></th>
            <th><?php echo 'Количество / Отсканировали'; ?></th>
        </tr>
        <?php foreach($data as $value) { ?>
            <tr >
                <td style="background-color: #ff9393 !important;">
                    <?php
                        $cdItem = CrossDockItems::findOne($value->id);
                        $storeName = '';
                        if($cdItem) {
                            $cd = CrossDock::findOne($cdItem->cross_dock_id);
                            $store = Store::findOne($cd->to_point_id);
                            $storeName = $store->getPointTitleByPattern('{shopping_center_name} / {shop_code} / {city_name}');
                        }
                        echo $storeName;
                    ?>
                </td>
                <td style="background-color: #ff9393 !important;">
                    <?php
                    $title = '';
                    if($cdItem) {
                        $title = $cdItem->box_barcode;
                    }
                    echo  $title;
                    ?>
                </td>
                <td style="background-color: #ff9393 !important;"><?php //echo $value->product_barcode ?></td>
                <td style="background-color: #ff9393 !important;"><?php //echo $value->expected_qty ?></td>
            </tr>
<!--            --><?php //$boxes = CrossDockItemProducts::find()->andWhere('product_barcode != :product_barcode',['product_barcode'=>$value->product_barcode])->andWhere(['cross_dock_item_id'=>$value->id])->all(); ?>
            <?php $boxes = CrossDockItemProducts::find()->andWhere(['cross_dock_item_id'=>$value->id])->all(); ?>
            <?php if($boxes) { ?>
                <?php foreach($boxes as $box) { ?>
                    <tr>
                        <td>-</td>
                        <td>-</td>
<!--                        <td>--><?php //echo $title?><!--</td>-->
                        <td <?php echo (isset($scannedProductsWhere[$box->product_barcode]) ? 'style="background-color: #ff9393 !important;"' : '') ?>><?php echo $box->product_barcode ?></td>
                        <td><?php echo $box->expected_qty ?> <?php echo " / ".(isset($scannedProductsWhere[$box->product_barcode]) ? $scannedProductsWhere[$box->product_barcode] : ' 0') ?></td>
                    </tr>
                <?php } ?>
            <?php } ?>
        <?php } ?>
    </table>
</div>