<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 21.11.2016
 * Time: 10:50
 */

namespace stockDepartment\modules\wms\managers\defacto;


class InboundStatus extends ConsignmentUniversalStatus
{
    protected $allowedStatus = [
        self::STATUS_INBOUND_NEW,
        self::STATUS_INBOUND_LOADED_FROM_API,
        self::STATUS_INBOUND_LOADED_SAVED,
        self::STATUS_INBOUND_COMPLETE,
    ];

    public function __construct($status)
    {
        parent::__construct($status);
        $this->type = self::ORDER_TYPE_INBOUND;
    }
}