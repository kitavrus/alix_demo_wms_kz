<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 08.10.2019
 * Time: 10:04
 */
namespace common\ecommerce\defacto\changeAddressPlace\service;

use common\ecommerce\defacto\outbound\service\ReservationPlaceAddressSortingService;
use common\ecommerce\defacto\stock\service\Service;
use common\ecommerce\entities\EcommerceChangeAddressPlace;

/**
 * Class ChangeAddressPlaceService
 * @package common\ecommerce\defacto\сhangeAddressPlace\service
 */
class ChangeAddressPlaceService
{
    /**
     * @param $FromAddress
     * @param $ToAddress
     * @param string $ProductBarcode
     * @param int $ProductQty
     */
    public static function save($FromAddress,$ToAddress,$ProductBarcode = '',$ProductQty = 0) {
         $changeAddressPlace = new EcommerceChangeAddressPlace();
         $changeAddressPlace->from_barcode = $FromAddress;
         $changeAddressPlace->to_barcode = $ToAddress;
         $changeAddressPlace->product_barcode = $ProductBarcode;
         $changeAddressPlace->product_qty = $ProductQty;
         $changeAddressPlace->save(false);
    }

    public function changeBoxPlaceAddress($BoxBarcode,$PlaceBarcode) {
        (new \common\ecommerce\defacto\stock\service\Service())->changeBoxPlaceAddress($BoxBarcode,$PlaceBarcode);
        (new ReservationPlaceAddressSortingService())->changeBoxPlaceAddress($BoxBarcode,$PlaceBarcode);
        self::save($BoxBarcode,$PlaceBarcode);
    }

    public function moveProductFromBoxToBox($BoxBarcode,$PlaceBarcode) {
        $stockService = new \common\ecommerce\defacto\stock\service\Service();
        $stockService->changeBoxPlaceAddress($BoxBarcode,$PlaceBarcode);
        self::save($BoxBarcode,$PlaceBarcode);
    }
}
