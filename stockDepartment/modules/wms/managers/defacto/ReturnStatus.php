<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 21.11.2016
 * Time: 10:51
 */

namespace stockDepartment\modules\wms\managers\defacto;


class ReturnStatus extends ConsignmentUniversalStatus
{
    protected $allowedStatus = [
        self::STATUS_RETURN_NEW,
        self::STATUS_RETURN_LOADED,
        self::STATUS_RETURN_COMPLETE,
    ];

    public function __construct($status)
    {
        parent::__construct($status);
        $this->type = self::ORDER_TYPE_RETURN;
    }
}