<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 23.08.2017
 * Time: 9:56
 */

namespace common\managers\base;


use common\modules\client\models\Client;
use common\modules\stock\models\Stock;
use common\overloads\ArrayHelper;
use yii\helpers\VarDumper;

class AllocateManager
{
	    // Strategy clear empty box
    public static function strategyClearEmptyBox($skuID,$allocatedQty, $clientId = Client::CLIENT_DEFACTO)
//    public static function strategyClearEmptyBox($productBarcode,$allocatedQty, $clientId = Client::CLIENT_DEFACTO)
    {

		$productBarcodeArray = Stock::find()->distinct()->select('product_barcode')->andWhere([
			'field_extra1'=>$skuID,
//            'product_barcode'=>$productBarcode,
			'client_id'=>$clientId,
			'status_availability'=>Stock::STATUS_AVAILABILITY_YES,
		])
		 ->column();

        $primaryAddress = Stock::find()->distinct()->select('primary_address')->andWhere([
            'field_extra1'=>$skuID,
//            'product_barcode'=>$productBarcode,
            'client_id'=>$clientId,
            'status_availability'=>Stock::STATUS_AVAILABILITY_YES,
        ])
            //->andWhere("secondary_address != '6-1-08-4'")
           // ->andWhere("secondary_address != '5-1-06-0'")
            ->column();

        $stockIDs = [];
        $inPrimaryAddress = '';
		$inProductBarcodeArray = '';
        if(empty($primaryAddress)) {
            return $stockIDs;
        }

        foreach ($productBarcodeArray as $item) {
            $inProductBarcodeArray .= "'".$item."',";
        }
		$inProductBarcodeArray = trim($inProductBarcodeArray,',');

        foreach ($primaryAddress as $item) {
            $inPrimaryAddress .= "'".$item."',";
        }
        $inPrimaryAddress = trim($inPrimaryAddress,',');

        $connection = \Yii::$app->getDb();


        $command = $connection->createCommand("SET group_concat_max_len=4096;");
        $command->execute();

        $command = $connection->createCommand("SELECT
	count(product_barcode) as qtyInBox,
	(	SELECT
			count(suStock.product_barcode) as totalQtyInBox
			FROM stock as suStock
			WHERE
				suStock.productBarcode in (:productBarcode) AND
                suStock.primary_address = s.primary_address  AND
                suStock.client_id = s.client_id AND
                suStock.status_availability = s.status_availability
				 GROUP BY suStock.primary_address
	) as productQtyInBox
,	primary_address,
	secondary_address,
	address_sort_order,
	(SELECT
			group_concat(groupConCatStock.id )
			FROM stock as groupConCatStock
			WHERE
				groupConCatStock.productBarcode in (:productBarcode) AND
                groupConCatStock.primary_address = s.primary_address  AND
                groupConCatStock.client_id = s.client_id AND
                groupConCatStock.status_availability = s.status_availability
				 ) as stockIds
	FROM stock as s
	WHERE
		primary_address in (:inPrimaryAddress) AND
		client_id = :clientId AND
        status_availability = :statusAvailability
	GROUP BY primary_address
	ORDER BY qtyInBox,address_sort_order
", [
            ':productBarcode' => $inProductBarcodeArray,
            ':inPrimaryAddress' => $inPrimaryAddress,
//			':field_extra1'=>$skuID,
            ':clientId' => $clientId,
            ':statusAvailability' => Stock::STATUS_AVAILABILITY_YES,
        ]);

        $queryResult = $command->queryAll();

        if (empty($queryResult)) {
            return $stockIDs;
        }
        $currentAllocatedQty = 0;
        // максимально очищаем короба
        foreach($queryResult as $key=>$item) {

            if($item['qtyInBox'] == $item['productQtyInBox']) {
                if($currentAllocatedQty == $allocatedQty) {
                    return $stockIDs;
                }

                $stockIdsInBox = explode(',',$item['stockIds']);
                if(empty($stockIdsInBox)) {
                    break;
                }

                foreach ($stockIdsInBox as $stockIDInBoxItem) {
                    if($currentAllocatedQty == $allocatedQty) {
                        return $stockIDs;
                    }
                    $currentAllocatedQty += 1;
                    $stockIDs = ArrayHelper::merge($stockIDs,[$stockIDInBoxItem]);
                }
//                if($currentAllocatedQty == $allocatedQty) {
//                    return $stockIDs;
//                }
//                unset($queryResult[$key]);
            }
        }

        // уже нет коробов которые можно очистить
        if($currentAllocatedQty != $allocatedQty) {
            foreach($queryResult as $key=>$item) {
                if($item['qtyInBox'] != $item['productQtyInBox']) {
                    if($currentAllocatedQty == $allocatedQty) {
                        return $stockIDs;
                    }

                    $stockIdsInBox = explode(',',$item['stockIds']);
                    if(empty($stockIdsInBox)) {
                        break;
                    }

                    foreach ($stockIdsInBox as $stockIDInBoxItem) {
                        if($currentAllocatedQty == $allocatedQty) {
                            return $stockIDs;
                        }
                        $currentAllocatedQty += 1;
                        $stockIDs = ArrayHelper::merge($stockIDs,[$stockIDInBoxItem]);
                    }
//                    unset($queryResult[$key]);
                }
            }
        }

        return $stockIDs;

//        VarDumper::dump($queryResult, 10, true);
//        echo "<br />";
//        VarDumper::dump($stockIDs, 10, true);
//        echo "<br />";
//        VarDumper::dump($currentAllocatedQty, 10, true);
//        die;
    }    // Strategy clear empty box


    public static function strategyClearEmptyBoxByProduct_ZXCZXCZX($productBarcode,$allocatedQty, $clientId = Client::CLIENT_DEFACTO)
    {

//		$productBarcodeArray = Stock::find()->distinct()->select('product_barcode')->andWhere([
//			'field_extra1'=>$skuID,
////            'product_barcode'=>$productBarcode,
//			'client_id'=>$clientId,
//			'status_availability'=>Stock::STATUS_AVAILABILITY_YES,
//		])
//		 ->column();

        $primaryAddress = Stock::find()->distinct()->select('primary_address')->andWhere([
//            'field_extra1'=>$skuID,
            'product_barcode'=>$productBarcode,
            'client_id'=>$clientId,
            'status_availability'=>Stock::STATUS_AVAILABILITY_YES,
        ])
            //->andWhere("secondary_address != '6-1-08-4'")
           // ->andWhere("secondary_address != '5-1-06-0'")
            ->column();

        $stockIDs = [];
        $inPrimaryAddress = '';
//		$inProductBarcodeArray = '';
        if(empty($primaryAddress)) {
            return $stockIDs;
        }

//        foreach ($productBarcodeArray as $item) {
//            $inProductBarcodeArray .= "'".$item."',";
//        }
//		$inProductBarcodeArray = trim($inProductBarcodeArray,',');

        foreach ($primaryAddress as $item) {
            $inPrimaryAddress .= "'".$item."',";
        }
        $inPrimaryAddress = trim($inPrimaryAddress,',');

        $connection = \Yii::$app->getDb();


        $command = $connection->createCommand("SET group_concat_max_len=4096;");
        $command->execute();

        $command = $connection->createCommand("SELECT
	count(product_barcode) as qtyInBox,
	(	SELECT
			count(suStock.product_barcode) as totalQtyInBox
			FROM stock as suStock
			WHERE
				suStock.product_barcode = :productBarcode AND
                suStock.primary_address = s.primary_address  AND
                suStock.client_id = s.client_id AND
                suStock.status_availability = s.status_availability
				 GROUP BY suStock.primary_address
	) as productQtyInBox
,	primary_address,
	secondary_address,
	address_sort_order,
	(SELECT
			group_concat(groupConCatStock.id )
			FROM stock as groupConCatStock
			WHERE
				groupConCatStock.product_barcode = :productBarcode AND
                groupConCatStock.primary_address = s.primary_address  AND
                groupConCatStock.client_id = s.client_id AND
                groupConCatStock.status_availability = s.status_availability
				 ) as stockIds
	FROM stock as s
	WHERE
		primary_address in (:inPrimaryAddress) AND
		client_id = :clientId AND
        status_availability = :statusAvailability
	GROUP BY primary_address
	ORDER BY qtyInBox,address_sort_order
", [
            ':productBarcode' => $productBarcode,
            ':inPrimaryAddress' => $inPrimaryAddress,
            ':clientId' => $clientId,
            ':statusAvailability' => Stock::STATUS_AVAILABILITY_YES,
        ]);

        $queryResult = $command->queryAll();

        if (empty($queryResult)) {
            return $stockIDs;
        }
        $currentAllocatedQty = 0;
        // максимально очищаем короба
        foreach($queryResult as $key=>$item) {

            if($item['qtyInBox'] == $item['productQtyInBox']) {
                if($currentAllocatedQty == $allocatedQty) {
                    return $stockIDs;
                }

                $stockIdsInBox = explode(',',$item['stockIds']);
                if(empty($stockIdsInBox)) {
                    break;
                }

                foreach ($stockIdsInBox as $stockIDInBoxItem) {
                    if($currentAllocatedQty == $allocatedQty) {
                        return $stockIDs;
                    }
                    $currentAllocatedQty += 1;
                    $stockIDs = ArrayHelper::merge($stockIDs,[$stockIDInBoxItem]);
                }
//                if($currentAllocatedQty == $allocatedQty) {
//                    return $stockIDs;
//                }
//                unset($queryResult[$key]);
            }
        }

        // уже нет коробов которые можно очистить
        if($currentAllocatedQty != $allocatedQty) {
            foreach($queryResult as $key=>$item) {
                if($item['qtyInBox'] != $item['productQtyInBox']) {
                    if($currentAllocatedQty == $allocatedQty) {
                        return $stockIDs;
                    }

                    $stockIdsInBox = explode(',',$item['stockIds']);
                    if(empty($stockIdsInBox)) {
                        break;
                    }

                    foreach ($stockIdsInBox as $stockIDInBoxItem) {
                        if($currentAllocatedQty == $allocatedQty) {
                            return $stockIDs;
                        }
                        $currentAllocatedQty += 1;
                        $stockIDs = ArrayHelper::merge($stockIDs,[$stockIDInBoxItem]);
                    }
//                    unset($queryResult[$key]);
                }
            }
        }

        return $stockIDs;

//        VarDumper::dump($queryResult, 10, true);
//        echo "<br />";
//        VarDumper::dump($stockIDs, 10, true);
//        echo "<br />";
//        VarDumper::dump($currentAllocatedQty, 10, true);
//        die;
    }
	
	
	
	
    // Strategy clear empty box
    public static function strategyClearEmptyBoxByProduct($productBarcode,$allocatedQty, $clientId = Client::CLIENT_DEFACTO)
    {

        $primaryAddress = Stock::find()->distinct()->select('primary_address')->andWhere([
            'product_barcode'=>$productBarcode,
            'client_id'=>$clientId,
            'status_availability'=>Stock::STATUS_AVAILABILITY_YES,
        ])
		//->andWhere("secondary_address != '6-1-08-4'") // 6-1-08-4 парфюмерии 3 коробка
        //->andWhere("secondary_address != '5-1-06-0'") // 5-1-06-0 шампунь,
		->column();
		


        $stockIDs = [];
        $inPrimaryAddress = '';
        if(empty($primaryAddress)) {
            return $stockIDs;
        }

        foreach ($primaryAddress as $item) {
            $inPrimaryAddress .= "'".$item."',";
        }
        $inPrimaryAddress = trim($inPrimaryAddress,',');

        $connection = \Yii::$app->getDb();


        $command = $connection->createCommand("SET group_concat_max_len=4096;");
        $command->execute();

        $command = $connection->createCommand("SELECT
	count(product_barcode) as qtyInBox,
	(	SELECT
			count(suStock.product_barcode) as totalQtyInBox
			FROM stock as suStock
			WHERE
				suStock.product_barcode = :productBarcode AND
                suStock.primary_address = s.primary_address  AND
                suStock.client_id = s.client_id AND
                suStock.status_availability = s.status_availability
				 GROUP BY suStock.primary_address
	) as productQtyInBox
,	primary_address,
	secondary_address,
	address_sort_order,
	(SELECT
			group_concat(groupConCatStock.id )
			FROM stock as groupConCatStock
			WHERE
				groupConCatStock.product_barcode = :productBarcode AND
                groupConCatStock.primary_address = s.primary_address  AND
                groupConCatStock.client_id = s.client_id AND
                groupConCatStock.status_availability = s.status_availability
				 ) as stockIds
	FROM stock as s
	WHERE
		primary_address in (".$inPrimaryAddress.") AND
		client_id = :clientId AND
        status_availability = :statusAvailability
	GROUP BY primary_address
	ORDER BY qtyInBox,address_sort_order
", [
            ':productBarcode' => $productBarcode,
            ':clientId' => $clientId,
            ':statusAvailability' => Stock::STATUS_AVAILABILITY_YES,
        ]);

        $queryResult = $command->queryAll();

        if (empty($queryResult)) {
            return $stockIDs;
        }
        $currentAllocatedQty = 0;
        // максимально очищаем короба
        foreach($queryResult as $key=>$item) {

            if($item['qtyInBox'] == $item['productQtyInBox']) {
                if($currentAllocatedQty == $allocatedQty) {
                    return $stockIDs;
                }

                $stockIdsInBox = explode(',',$item['stockIds']);
                if(empty($stockIdsInBox)) {
                    break;
                }

                foreach ($stockIdsInBox as $stockIDInBoxItem) {
                    if($currentAllocatedQty == $allocatedQty) {
                        return $stockIDs;
                    }
                    $currentAllocatedQty += 1;
                    $stockIDs = ArrayHelper::merge($stockIDs,[$stockIDInBoxItem]);
                }
//                if($currentAllocatedQty == $allocatedQty) {
//                    return $stockIDs;
//                }
//                unset($queryResult[$key]);
            }
        }

        // уже нет коробов которые можно очистить
        if($currentAllocatedQty != $allocatedQty) {
            foreach($queryResult as $key=>$item) {
                if($item['qtyInBox'] != $item['productQtyInBox']) {
                    if($currentAllocatedQty == $allocatedQty) {
                        return $stockIDs;
                    }

                    $stockIdsInBox = explode(',',$item['stockIds']);
                    if(empty($stockIdsInBox)) {
                        break;
                    }

                    foreach ($stockIdsInBox as $stockIDInBoxItem) {
                        if($currentAllocatedQty == $allocatedQty) {
                            return $stockIDs;
                        }
                        $currentAllocatedQty += 1;
                        $stockIDs = ArrayHelper::merge($stockIDs,[$stockIDInBoxItem]);
                    }
//                    unset($queryResult[$key]);
                }
            }
        }

        return $stockIDs;

//        VarDumper::dump($queryResult, 10, true);
//        echo "<br />";
//        VarDumper::dump($stockIDs, 10, true);
//        echo "<br />";
//        VarDumper::dump($currentAllocatedQty, 10, true);
//        die;
    }
	
		// Временное решение
	public static function strategyClearEmptyBoxByProductErenRetail($productBarcode,$allocatedQty, $clientId = Client::CLIENT_ERENRETAIL)
	{
		$stockIDs = Stock::find()->select('id')->andWhere([
			'product_barcode'=>$productBarcode,
			'client_id'=>$clientId,
			'status_availability'=>Stock::STATUS_AVAILABILITY_YES,
		])->orderBy("address_sort_order");
//		  ->limit($allocatedQty);

		return $stockIDs;
	}
	

    // Strategy fast allocated
    public static function strategyFastAllocated($productBarcode)
    {
    }
}