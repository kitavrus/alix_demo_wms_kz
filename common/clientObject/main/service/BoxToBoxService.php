<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 07.10.2017
 * Time: 14:47
 */

namespace common\clientObject\main\service;


use common\modules\stock\models\Stock;

class BoxToBoxService
{
    public static function boxToBox($fromBox,$productBarcode,$toBox) {
	  
	  file_put_contents('BoxToBoxService.log',$fromBox.';'.$toBox.';'.$productBarcode.';'."\n",FILE_APPEND);
		
      Stock::updateAll(['primary_address'=>$toBox],
          [
              'primary_address'=>$fromBox,
              'product_barcode'=>$productBarcode,
              'status_availability'=>Stock::STATUS_AVAILABILITY_YES,
          ]
      );
    }
}