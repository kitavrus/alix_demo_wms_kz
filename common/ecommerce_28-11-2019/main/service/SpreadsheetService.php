<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 23.11.2017
 * Time: 12:39
 */

namespace common\ecommerce\main\service;


use yii\helpers\VarDumper;

class SpreadsheetService
{
    public static function parseFileCSV($pathToFile)
    {
        $order = new \stdClass();
        $order->totalQtyRows = 0;
        $order->expectedTotalProductQty = 0;
        $order->expectedTotalPlaceQty = 0;
        $order->items = [];

        if (file_exists($pathToFile)) {
            if (($handle = fopen($pathToFile, "r")) !== FALSE) {
                $iRow = 0;
                while (($rowFromFile = fgetcsv($handle, 1000, ";")) !== FALSE) {
                    $iRow++;
                    $rowItem = isset($rowFromFile[0]) ? trim($rowFromFile[0]) : '';
                    $productBarcode = isset($rowFromFile[1]) ? trim($rowFromFile[1]) : '';
                    $productName = isset($rowFromFile[2]) ? trim($rowFromFile[2]) : '';
                    $productQty = isset($rowFromFile[3]) ? intval(trim($rowFromFile[3])) : 0;

                    if($productBarcode == null || $productName == null || $productQty == null) continue;
                    if($iRow <= 1 ) continue;

                    $row = new \stdClass();
                    $row->row = $rowItem;
                    $row->productBarcode = $productBarcode;
                    $row->productModel = $productBarcode;
                    $row->productName = $productName;
                    $row->expectedProductQty = $productQty;
                    $row->expectedPlaceQty = 0;

                    $order->totalQtyRows += 1;
                    $order->expectedTotalProductQty += $row->expectedProductQty;

                    if (isset($order->items[$productBarcode])) {
                        $order->items[$productBarcode]->expectedProductQty += $row->expectedProductQty;
                    } else {
                        $order->items[$productBarcode] = $row;
                    }

                }
            }
        }

        return $order;
    }

    public static function parseFile($pathToFile)
    {
        $excel =\PhpOffice\PhpSpreadsheet\IOFactory::load($pathToFile);
        $excel->setActiveSheetIndex(0);
        $excelActive = $excel->getActiveSheet();

        $order = new \stdClass();
        $order->totalQtyRows = 0;
        $order->expectedTotalProductQty = 0;
        $order->expectedTotalPlaceQty = 0;
        $order->items = [];

        $start = 2;
        for ($i = $start; $i <= 1000; $i++)  {

            $rowItem = (int)$excelActive->getCell('A' . $i)->getValue();
            $productBarcode = (string)$excelActive->getCell('B' . $i)->getValue();
            $productName = (string)$excelActive->getCell('C' . $i)->getValue();
            $productQty = (int)$excelActive->getCell('D' . $i)->getValue();

            if($productBarcode == null || $productName == null || $productQty == null ) continue;

            $row = new \stdClass();
            $row->row = $rowItem;
            $row->productBarcode = self::preparedProductBarcode($productBarcode);
            $row->productModel = $row->productBarcode;
            $row->productName = $productName;
            $row->expectedProductQty = $productQty;
            $row->expectedPlaceQty = 0;

            $order->totalQtyRows += 1;
            $order->expectedTotalProductQty += $row->expectedProductQty;

            if (isset($order->items[$productBarcode])) {
                $order->items[$productBarcode]->expectedProductQty += $row->expectedProductQty;
            } else {
                $order->items[$productBarcode] = $row;
            }
        }

        return $order;
    }

    public static function preparedProductBarcode($barcode) {

        $barcode = preg_replace('/\s/','',$barcode);
        $fixBarcode = [
          '143'=>   '00000000143',
          '35'=>    '00000000035',
          '20'=>    '00000000020',
          '163'=>   '00000000163',
          '161'=>   '00000000161',
          '36'=>    '00000000036',
          '346'=>   '00000000346',
          '298'=>   '00000000298',
          '349'=>   '00000000349',
          '296'=>   '00000000296',
          '297'=>   '00000000297',
          '625281'=>'00000625281',
          '625279'=>'00000625279',
          '82'=>    '00000000082',
          '625280'=>'00000625280',
          '21'    =>'00000000021',
        ];

        return array_key_exists($barcode,$fixBarcode) ? $fixBarcode[$barcode] : $barcode;
    }
}