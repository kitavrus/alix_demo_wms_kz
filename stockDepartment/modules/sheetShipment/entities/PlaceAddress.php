<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 07.06.2017
 * Time: 16:23
 */

namespace stockDepartment\modules\sheetShipment\entites;


class PlaceAddress
{
    private $placeAddress; // place address for any outbound orders

    /**
     * PlaceAddress constructor.
     * @param $placeAddress
     */
    public function __construct($placeAddress)
    {
        $this->placeAddress = $placeAddress;
    }

    /**
     * @return mixed
     */
    public function getPlaceAddress()
    {
        return $this->placeAddress;
    }

    /**
     * @param mixed $placeAddress
     */
    public function setPlaceAddress($placeAddress)
    {
        $this->placeAddress = $placeAddress;
    }
}