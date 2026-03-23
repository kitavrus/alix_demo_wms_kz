<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 07.09.2019
 * Time: 16:52
 */
namespace common\ecommerce\defacto\inbound\service;


class BarcodeService_
{
    /**
     * @param $aBoxBarcode
     * @return bool
     */
    public function isOurBoxBarcode($aBoxBarcode) {
        $prefix = $this->getPrefixBarcode($aBoxBarcode);
        return (!empty($prefix) && $prefix == '70' && (strlen($aBoxBarcode) <=13 && strlen($aBoxBarcode) >=9));
    }

    /*
    * Get prefix barcode
    * @param string $barcode
    * @return string Prefix
    * */
    private function getPrefixBarcode($barcode)
    {
        $barcode = trim($barcode);
        return !empty($barcode) ? substr($barcode, 0, 2) : '';
    }
}