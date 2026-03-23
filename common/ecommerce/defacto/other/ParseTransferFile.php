<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 05.05.2020
 * Time: 11:08
 */

namespace common\ecommerce\defacto\other;


class ParseTransferFile
{
    public function parseFileResult($aPathToFile) {

        $rootPath = \Yii::getAlias('@stockDepartment').'/web/tmp-file/defacto/b2c-b2b/'.$aPathToFile;
        $excel = \PHPExcel_IOFactory::load($rootPath);
        $excel->setActiveSheetIndex(0);
        $excelActive = $excel->getActiveSheet();

        $reportFrom = [];
        $start = 2;
        for ($i = $start; $i <= 51462; $i++) {

            $OurBoxBarcode = (string)$excelActive->getCell('A' . $i)->getValue();
            $PlaceAddress = (string)$excelActive->getCell('B' . $i)->getValue();
            $lcBarcode = (string)$excelActive->getCell('E' . $i)->getValue();
            $productBarcodeExpected = (string)$excelActive->getCell('D' . $i)->getValue();
            $productBarcodeScanned = (string)$excelActive->getCell('F' . $i)->getValue();
            $sku = (string)$excelActive->getCell('I' . $i)->getValue();
            $problem = (string)$excelActive->getCell('G' . $i)->getValue();
            $problemType = (string)$excelActive->getCell('H' . $i)->getValue();
            $productSeason = (string)$excelActive->getCell('J' . $i)->getValue();

            $reportFrom[] = [
                'ourBoxBarcode'=>trim($OurBoxBarcode),
                'placeAddress'=>trim($PlaceAddress),
                'lcBarcode'=>trim($lcBarcode),
                'productBarcodeExpected'=>trim($productBarcodeExpected),
                'productBarcodeScanned'=>trim($productBarcodeScanned),
                'problem'=>trim($problem),
                'problemType'=>trim($problemType),
                'sku'=>trim($sku),
                'productSeason'=>trim($productSeason),
            ];
        }

        $firstStep = [];
        foreach($reportFrom as $item) {
            if(empty($item['lcBarcode'])) {
                continue;
            }

            $firstStep[$item['lcBarcode']][] = $item;
        }

        return $firstStep;
    }

    public function parseFileByOurBoxResult($aPathToFile) {

        $rootPath = \Yii::getAlias('@stockDepartment').'/web/tmp-file/defacto/b2c-b2b/'.$aPathToFile;
        $excel = \PHPExcel_IOFactory::load($rootPath);
        $excel->setActiveSheetIndex(0);
        $excelActive = $excel->getActiveSheet();

        $reportFrom = [];
        $start = 2;
        for ($i = $start; $i <= 51462; $i++) {

            $OurBoxBarcode = (string)$excelActive->getCell('A' . $i)->getValue();
            $PlaceAddress = (string)$excelActive->getCell('B' . $i)->getValue();
            $lcBarcode = (string)$excelActive->getCell('E' . $i)->getValue();
            $productBarcodeExpected = (string)$excelActive->getCell('D' . $i)->getValue();
            $productBarcodeScanned = (string)$excelActive->getCell('F' . $i)->getValue();
            $sku = (string)$excelActive->getCell('I' . $i)->getValue();
            $problem = (string)$excelActive->getCell('G' . $i)->getValue();
            $problemType = (string)$excelActive->getCell('H' . $i)->getValue();
            $productSeason = (string)$excelActive->getCell('J' . $i)->getValue();

            $reportFrom[] = [
                'ourBoxBarcode'=>trim($OurBoxBarcode),
                'placeAddress'=>trim($PlaceAddress),
                'lcBarcode'=>trim($lcBarcode),
                'productBarcodeExpected'=>trim($productBarcodeExpected),
                'productBarcodeScanned'=>trim($productBarcodeScanned),
                'problem'=>trim($problem),
                'problemType'=>trim($problemType),
                'sku'=>trim($sku),
                'productSeason'=>trim($productSeason),
            ];
        }

        $firstStep = [];
        foreach($reportFrom as $item) {
            if(empty($item['ourBoxBarcode'])) {
                continue;
            }

            $firstStep[$item['ourBoxBarcode']][] = $item;
        }

        return $firstStep;
    }

    public function calculateProductInBox($aPreparedData) {

        $secondStep = [];
        $result = [];
        foreach($aPreparedData as $ourBoxBarcode=>$items) {
            foreach ($items as $k=>$item) {
                if(!isset($secondStep[$item['productBarcodeScanned']])) {
                    $secondStep[$item['productBarcodeScanned']] = $item;
                    $secondStep[$item['productBarcodeScanned']]['productBarcodeScannedQty'] = 1;
                } else {
                    $secondStep[$item['productBarcodeScanned']]['productBarcodeScannedQty'] += 1;
                }
            }

            $result[$ourBoxBarcode] = $secondStep;
            $secondStep = [];
        }

       return $result;
    }


    public function makeOurFormat($aPreparedData,$aLcBarcodeList) {
        $rowToFile = "Our Box Barcode".';'
            ."Place Address".';'
            ."Our Box Barcode".';'
            ."Product Barcode Expected".';'
            ."LC Barcode".';'
            ."Product Barcode Scanned".';'
            ."Problem".';'
            ."Problem Type".';'
            ."Product SKU".';'
            ."Product SEASON".';'
            ."Product Qty".';'
            ."\n";

        $fileName = 'OurFormat-05-05-2020-v5.csv';
        file_put_contents($fileName,$rowToFile);
        $productQty = 0;
        foreach($aPreparedData as $lcBox=>$items) {
            if(isset($aLcBarcodeList[$lcBox])) {
                continue;
            }

            foreach($items as $roductBarcode=>$item) {

                if(!empty($item['productBarcodeScanned'])) {
                    ++$productQty;
                }

                $row = $item['ourBoxBarcode'].';'.
                       $item['placeAddress'].';'.
                       $item['ourBoxBarcode'].';'.
                       $item['productBarcodeExpected'].';'.
                       $item['lcBarcode'].';'.
                       $item['productBarcodeScanned'].';'.
                       $item['problem'].';'.
                       $item['problemType'].';'.
                       $item['sku'].';'.
                       $item['productSeason'].';'.
                       $productQty.';'
                ;

                file_put_contents($fileName,$row."\n",FILE_APPEND);
            }
            $row = ''."\n";
            file_put_contents($fileName,$row,FILE_APPEND);
        }
    }

    public function makeCerenFormat($aPreparedData,$aLcBarcodeList) {

        $row = 'LC Barcodes	Scanned'.';'.'Barcodes (EAN Codes)'.';'.'SkuID'.';'.'Quantity'.';';
        $fileName = 'CerenFormat-05-05-2020-v5.csv';
        file_put_contents($fileName,$row."\n");

        foreach($aPreparedData as $lcBox=>$items) {
            if(isset($aLcBarcodeList[$lcBox])) {
                continue;
            }

            foreach($items as $roductBarcode=>$item) {
                if(empty($item['productBarcodeScanned'])) {
                    continue;
                }

                $row = $item['lcBarcode'].';'.$item['productBarcodeScanned'].';'.$item['sku'].';'.$item['productBarcodeScannedQty'].';';
                file_put_contents($fileName,$row."\n",FILE_APPEND);
            }
        }
    }

    public function makeAddOutBoxFormat($aPreparedData,$aLcBarcodeList) {

        $row = 'LC Barcodes	Scanned'.';'.'Barcodes (EAN Codes)'.';'.'SkuID'.';'.'Quantity'.';';
        $fileName = 'AddOutBoxFormat-05-05-2020-v5.csv';
        file_put_contents($fileName,$row."\n");

        foreach($aPreparedData as $lcBox=>$items) {
            if(isset($aLcBarcodeList[$lcBox])) {
                continue;
            }

            foreach($items as $roductBarcode=>$item) {
                if(empty($item['productBarcodeScanned'])) {
                    continue;
                }

                $row = $item['lcBarcode'].';'.$item['productBarcodeScanned'].';'.$item['sku'].';'.$item['productBarcodeScannedQty'].';';
                file_put_contents($fileName,$row."\n",FILE_APPEND);
            }
        }
    }
}