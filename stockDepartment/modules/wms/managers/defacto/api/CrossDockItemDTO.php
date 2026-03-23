<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 23.12.2016
 * Time: 8:42
 */

namespace stockDepartment\modules\wms\managers\defacto\api;


class CrossDockItemDTO
{
    private $id = '';
    private $boxBarcode = '';
    private $numberPlacesQty = '';
    private $boxM3 = '';
    private $weightNet = '';
    private $weightBrut = '';
    private $inBoundId = '';
    private $lotBarcode = '';
    private $partyNumber = '';

    /**
     * CrossDockItemDTO constructor.
     * @param string $id
     * @param string $boxBarcode
     * @param string $numberPlacesQty
     * @param string $boxM3
     * @param string $weightNet
     * @param string $weightBrut
     * @param string $inBoundId
     * @param string $lotBarcode
     * @param string $partyNumber
     */
    public function __construct($boxBarcode, $numberPlacesQty, $boxM3, $weightNet, $weightBrut, $inBoundId, $lotBarcode, $partyNumber)
    {
        $this->boxBarcode = $boxBarcode;
        $this->numberPlacesQty = $numberPlacesQty;
        $this->boxM3 = $boxM3;
        $this->weightNet = $weightNet;
        $this->weightBrut = $weightBrut;
        $this->inBoundId = $inBoundId;
        $this->lotBarcode = $lotBarcode;
        $this->partyNumber = $partyNumber;
    }

    /**
     * @return string
     */
    public function getBoxBarcode()
    {
        return $this->boxBarcode;
    }

    /**
     * @param string $boxBarcode
     */
    public function setBoxBarcode($boxBarcode)
    {
        $this->boxBarcode = $boxBarcode;
    }

    /**
     * @return string
     */
    public function getNumberPlacesQty()
    {
        return $this->numberPlacesQty;
    }

    /**
     * @param string $numberPlacesQty
     */
    public function setNumberPlacesQty($numberPlacesQty)
    {
        $this->numberPlacesQty = $numberPlacesQty;
    }

    /**
     * @return string
     */
    public function getBoxM3()
    {
        return $this->boxM3;
    }

    /**
     * @param string $boxM3
     */
    public function setBoxM3($boxM3)
    {
        $this->boxM3 = $boxM3;
    }

    /**
     * @return string
     */
    public function getWeightNet()
    {
        return $this->weightNet;
    }

    /**
     * @param string $weightNet
     */
    public function setWeightNet($weightNet)
    {
        $this->weightNet = $weightNet;
    }

    /**
     * @return string
     */
    public function getWeightBrut()
    {
        return $this->weightBrut;
    }

    /**
     * @param string $weightBrut
     */
    public function setWeightBrut($weightBrut)
    {
        $this->weightBrut = $weightBrut;
    }

    /**
     * @return string
     */
    public function getInBoundId()
    {
        return $this->inBoundId;
    }

    /**
     * @param string $inBoundId
     */
    public function setInBoundId($inBoundId)
    {
        $this->inBoundId = $inBoundId;
    }

    /**
     * @return string
     */
    public function getLotBarcode()
    {
        return $this->lotBarcode;
    }

    /**
     * @param string $lotBarcode
     */
    public function setLotBarcode($lotBarcode)
    {
        $this->lotBarcode = $lotBarcode;
    }

    /**
     * @return string
     */
    public function getPartyNumber()
    {
        return $this->partyNumber;
    }

    /**
     * @param string $partyNumber
     */
    public function setPartyNumber($partyNumber)
    {
        $this->partyNumber = $partyNumber;
    }
}