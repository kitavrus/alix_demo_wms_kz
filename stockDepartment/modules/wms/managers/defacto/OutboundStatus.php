<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 21.11.2016
 * Time: 10:50
 */

namespace stockDepartment\modules\wms\managers\defacto;


class OutboundStatus extends ConsignmentUniversalStatus
{
    protected $allowedStatus = [
        self::STATUS_OUTBOUND_NEW,
        self::STATUS_OUTBOUND_LOADED,
        self::STATUS_OUTBOUND_SAVED_AND_CREATE_ORDERS,
        self::STATUS_OUTBOUND_COMPLETE,
    ];

    public function __construct($status)
    {
        parent::__construct($status);
        $this->type = self::ORDER_TYPE_OUTBOUND;
    }
}