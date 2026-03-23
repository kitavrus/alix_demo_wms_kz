<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 30.08.2017
 * Time: 21:06
 */

namespace stockDepartment\modules\wms\models\defacto\PickList\repository;


class PickListConstants
{
    const STATUS_SCAN_NEW = 1;
    const STATUS_SCAN_IN_PROCESS = 2;
    const STATUS_SCAN_DONE = 3;
    
    const STOCK_SCAN_STATUS_NO = 1;
    const STOCK_SCAN_STATUS_YES = 2;
}