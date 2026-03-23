<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 05.04.2017
 * Time: 12:02
 */

namespace stockDepartment\modules\returnOrder\entities\TmpOrder;


class Status
{
    const OTHER = 0;
    const NO_SCANNED = 1;
    const SCANNED = 2;
    const SEND_TO_API = 3;
    const COMPLETE = 4;
}