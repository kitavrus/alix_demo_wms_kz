<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 23.12.2016
 * Time: 8:26
 */

namespace stockDepartment\modules\wms\managers\defacto\api;


class ProductSerializeDataDTO
{
    private $id = '';
    private $fromBusinessUnitId = '';
    private $lcOrCartonLabel = '';
    private $numberOfCartons = '';
    private $skuId = '';
    private $lotOrSingleBarcode = '';
    private $lotOrSingleQuantity = '';
    private $status = '';
    private $appointmentBarcode = '';
    private $toBusinessUnitId = '';
    private $flowType = '';

    /**
     * ProductSerializeDataDTO constructor.
     * @param string $id
     * @param string $fromBusinessUnitId
     * @param string $lcOrCartonLabel
     * @param string $numberOfCartons
     * @param string $skuId
     * @param string $lotOrSingleBarcode
     * @param string $lotOrSingleQuantity
     * @param string $status
     * @param string $appointmentBarcode
     * @param string $toBusinessUnitId
     * @param string $flowType
     */
    public function __construct($id, $fromBusinessUnitId, $lcOrCartonLabel, $numberOfCartons, $skuId, $lotOrSingleBarcode, $lotOrSingleQuantity, $status, $appointmentBarcode, $toBusinessUnitId, $flowType)
    {
        $this->id = $id;
        $this->fromBusinessUnitId = $fromBusinessUnitId;
        $this->lcOrCartonLabel = $lcOrCartonLabel;
        $this->numberOfCartons = $numberOfCartons;
        $this->skuId = $skuId;
        $this->lotOrSingleBarcode = $lotOrSingleBarcode;
        $this->lotOrSingleQuantity = $lotOrSingleQuantity;
        $this->status = $status;
        $this->appointmentBarcode = $appointmentBarcode;
        $this->toBusinessUnitId = $toBusinessUnitId;
        $this->flowType = $flowType;
    }


    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getFromBusinessUnitId()
    {
        return $this->fromBusinessUnitId;
    }

    /**
     * @param string $fromBusinessUnitId
     */
    public function setFromBusinessUnitId($fromBusinessUnitId)
    {
        $this->fromBusinessUnitId = $fromBusinessUnitId;
    }

    /**
     * @return string
     */
    public function getLcOrCartonLabel()
    {
        return $this->lcOrCartonLabel;
    }

    /**
     * @param string $lcOrCartonLabel
     */
    public function setLcOrCartonLabel($lcOrCartonLabel)
    {
        $this->lcOrCartonLabel = $lcOrCartonLabel;
    }

    /**
     * @return string
     */
    public function getNumberOfCartons()
    {
        return $this->numberOfCartons;
    }

    /**
     * @param string $numberOfCartons
     */
    public function setNumberOfCartons($numberOfCartons)
    {
        $this->numberOfCartons = $numberOfCartons;
    }

    /**
     * @return string
     */
    public function getSkuId()
    {
        return $this->skuId;
    }

    /**
     * @param string $skuId
     */
    public function setSkuId($skuId)
    {
        $this->skuId = $skuId;
    }

    /**
     * @return string
     */
    public function getLotOrSingleBarcode()
    {
        return $this->lotOrSingleBarcode;
    }

    /**
     * @param string $lotOrSingleBarcode
     */
    public function setLotOrSingleBarcode($lotOrSingleBarcode)
    {
        $this->lotOrSingleBarcode = $lotOrSingleBarcode;
    }

    /**
     * @return string
     */
    public function getLotOrSingleQuantity()
    {
        return $this->lotOrSingleQuantity;
    }

    /**
     * @param string $lotOrSingleQuantity
     */
    public function setLotOrSingleQuantity($lotOrSingleQuantity)
    {
        $this->lotOrSingleQuantity = $lotOrSingleQuantity;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getAppointmentBarcode()
    {
        return $this->appointmentBarcode;
    }

    /**
     * @param string $appointmentBarcode
     */
    public function setAppointmentBarcode($appointmentBarcode)
    {
        $this->appointmentBarcode = $appointmentBarcode;
    }

    /**
     * @return string
     */
    public function getToBusinessUnitId()
    {
        return $this->toBusinessUnitId;
    }

    /**
     * @param string $toBusinessUnitId
     */
    public function setToBusinessUnitId($toBusinessUnitId)
    {
        $this->toBusinessUnitId = $toBusinessUnitId;
    }

    /**
     * @return string
     */
    public function getFlowType()
    {
        return $this->flowType;
    }

    /**
     * @param string $flowType
     */
    public function setFlowType($flowType)
    {
        $this->flowType = $flowType;
    }
}