<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 04.02.15
 * Time: 12:05
 */

//use Yii;
use common\modules\outbound\models\OutboundOrder;
use common\modules\outbound\models\OutboundOrderItem;
use common\modules\stock\models\Stock;
use common\modules\store\models\Store;





//S: Start test load demo data
$pathToCSVFile = Yii::getAlias('@stockDepartment/tests/_data/client/de-facto/in-stock-import.csv');
$row = 1;
$arrayToSaveCSVFile = [];
if ( ($handle = fopen($pathToCSVFile, "r")) !== FALSE ) {
    while ( ($data = fgetcsv($handle, 1000, ";")) !== FALSE ) {
//          $num = count($data);
//            echo "<p> $num fields in line $row: <br /></p>\n";

            $row++;

           if ( $row > 2 ) {


               \yii\helpers\VarDumper::dump($data,10,true);

                $stock = new Stock();
                $stock->inbound_order_id = 0;
                $stock->product_barcode = $data['0'];
                $stock->product_model = $data['8'];
                $stock->primary_address = $data['4'];
                $stock->secondary_address = $data['17'];
                $stock->status = Stock::STATUS_INBOUND_NEW;
                $stock->status_availability = Stock::STATUS_AVAILABILITY_YES;
                $stock->save(false);

                }
    }

    fclose($handle);
    // TRUNCATE TABLE `outbound_orders`
    // TRUNCATE TABLE `outbound_order_items`
}