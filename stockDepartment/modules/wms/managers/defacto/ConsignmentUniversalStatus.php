<?php
namespace stockDepartment\modules\wms\managers\defacto;


interface ConsignmentUniversalStatusInterface {
    public function __construct($status);
    public function getStatus();
    public function getType();
}

abstract class ConsignmentUniversalStatus implements ConsignmentUniversalStatusInterface
{
    /*
    * @var integer STATUS
    * */
    const STATUS_INBOUND_NEW = 0;
    const STATUS_INBOUND_LOADED_FROM_API = 2;
    const STATUS_INBOUND_LOADED_SAVED = 3;
    const STATUS_INBOUND_COMPLETE = 4;
    const STATUS_INBOUND_NOTIFY_IF_NEW_ORDER = 5;

    const STATUS_OUTBOUND_NEW = 10;
    const STATUS_OUTBOUND_LOADED = 11;
    const STATUS_OUTBOUND_SAVED_AND_CREATE_ORDERS = 12;
    const STATUS_OUTBOUND_COMPLETE = 13;

    const STATUS_RETURN_NEW = 20;
    const STATUS_RETURN_LOADED = 21;
    const STATUS_RETURN_COMPLETE = 22;

    /*
     * @var integer ORDER TYPE
     * */
    const ORDER_TYPE_INBOUND = 1;
    const ORDER_TYPE_OUTBOUND = 2;
    const ORDER_TYPE_RETURN = 3;

    protected $status;
    protected $type;
    protected $allowedStatus = [];

    public function __construct($status)
    {
        if (!in_array($status, $this->allowedStatus)) {
            throw new \InvalidArgumentException();
        }
        $this->status = $status;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getType()
    {
        return $this->type;
    }
}