<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 07.10.2017
 * Time: 14:47
 */

namespace common\clientObject\hyundaiAuto\inbound\service;



class PlaceToAddressService
{
    private $dto;
    private $inboundUnitAddressService;

    /**
     * PlaceToAddressService constructor.
     * @param $dto array | \stdClass
     */
    public function __construct($dto = []) {
        $this->dto = $dto;
        $this->placementUnitService = new \common\modules\placementUnit\service\Service();
        $this->inboundUnitAddressService = new \common\modules\placementUnit\service\inboundUnitAddressService();
    }

    public function placementToAddress() {
        // если это обычный адрес транспортный то меняем его на примари и ожидаем аддресс на 50
        // если это обычный адрес то ожидаем что это адрес полки
        if($this->placementUnitService->isExist($this->dto->fromPlaceAddress)) {
            $this->placementUnitService->setPrimaryAddress($this->dto->fromPlaceAddress,$this->dto->toPlaceAddress);
            $this->placementUnitService->setStatusFree($this->dto->fromPlaceAddress);
        }

        if($this->inboundUnitAddressService->isExist($this->dto->fromPlaceAddress)) {
            $this->placementUnitService->setSecondaryAddress($this->dto->fromPlaceAddress,$this->dto->toPlaceAddress);
        }

    }
}