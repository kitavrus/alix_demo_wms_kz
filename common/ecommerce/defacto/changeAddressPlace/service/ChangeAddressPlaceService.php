<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 08.10.2019
 * Time: 10:04
 */
namespace common\ecommerce\defacto\changeAddressPlace\service;

use common\ecommerce\constants\StockAvailability;
use common\ecommerce\defacto\outbound\service\ReservationPlaceAddressSortingService;
use common\ecommerce\defacto\stock\service\Service;
use common\ecommerce\entities\EcommerceChangeAddressPlace;
use common\ecommerce\entities\EcommerceStock;

/**
 * Class ChangeAddressPlaceService
 * @package common\ecommerce\defacto\сhangeAddressPlace\service
 */
class ChangeAddressPlaceService
{
    private $repository;
    private $reservationPlaceAddressSortingService;
    /**
     * ChangeAddressPlaceService constructor.
     */
    public function __construct()
    {
        $this->repository = new \common\ecommerce\defacto\changeAddressPlace\repository\Repository();
        $this->reservationPlaceAddressSortingService = new ReservationPlaceAddressSortingService();
    }

    public function changeBoxPlaceAddress($BoxBarcode,$PlaceBarcode) {
        $this->repository->changeBoxPlaceAddress($BoxBarcode,$PlaceBarcode);
        $this->reservationPlaceAddressSortingService->changeBoxPlaceAddress($BoxBarcode,$PlaceBarcode);
        $this->repository->saveHistory($BoxBarcode,$PlaceBarcode);
    }

    public function moveProductFromBoxToBox($fromBoxBarcode,$productBarcode,$toBoxBarcode) {
        $this->repository->moveProductFromBoxToBox($fromBoxBarcode,$productBarcode,$toBoxBarcode);
        $this->repository->saveHistory($fromBoxBarcode,$toBoxBarcode,$productBarcode);
    }

    public function moveAllProductsFromBoxToBox($fromBoxBarcode,$toBoxBarcode) {
        $this->repository->moveAllProductsFromBoxToBox($fromBoxBarcode,$toBoxBarcode);
        $this->repository->saveHistory($fromBoxBarcode,$toBoxBarcode);
    }
}