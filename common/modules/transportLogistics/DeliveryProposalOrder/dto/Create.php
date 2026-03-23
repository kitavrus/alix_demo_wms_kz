<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 22.09.2017
 * Time: 8:12
 */

namespace common\modules\transportLogistics\DeliveryProposalOrder\dto;

/**
 * @property integer $id
 * @property integer $clientId
 * @property integer $deliveryProposalId
 * @property integer $orderType
 * @property integer $orderId
 * @property string  $orderNumber
 * @property integer $numberPlaces
 * @property integer $numberPlacesActual
 * @property integer $mc
 * @property integer $mcActual
 * @property integer $kg
 * @property integer $kgActual
 * @property integer $status
 * @property string $title
 * @property string $description
 */
class Create
{
    private $id;
    private $clientId;
    private $deliveryProposalId;
    private $orderType;
    private $orderId;
    private $orderNumber;
    private $numberPlaces;
    private $numberPlacesActual;
    private $mc;
    private $mcActual;
    private $kg;
    private $kgActual;
    private $status;
    private $title;
    private $description;

    /**
     * Create constructor.
     * @param int $id
     * @param int $clientId
     * @param int $deliveryProposalId
     * @param int $orderType
     * @param int $orderId
     * @param string $orderNumber
     * @param int $numberPlaces
     * @param int $numberPlacesActual
     * @param int $mc
     * @param int $mcActual
     * @param int $kg
     * @param int $kgActual
     * @param int $status
     * @param string $title
     * @param string $description
     */
    public function __construct($id, $clientId, $deliveryProposalId, $orderType, $orderId, $orderNumber, $numberPlaces, $numberPlacesActual, $mc, $mcActual, $kg, $kgActual, $status, $title, $description)
    {
        $this->id = $id;
        $this->clientId = $clientId;
        $this->deliveryProposalId = $deliveryProposalId;
        $this->orderType = $orderType;
        $this->orderId = $orderId;
        $this->orderNumber = $orderNumber;
        $this->numberPlaces = $numberPlaces;
        $this->numberPlacesActual = $numberPlacesActual;
        $this->mc = $mc;
        $this->mcActual = $mcActual;
        $this->kg = $kg;
        $this->kgActual = $kgActual;
        $this->status = $status;
        $this->title = $title;
        $this->description = $description;
    }

}