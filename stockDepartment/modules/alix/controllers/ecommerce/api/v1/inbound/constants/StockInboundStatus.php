<?php

namespace stockDepartment\modules\alix\controllers\ecommerce\api\v1\inbound\constants;

class StockInboundStatus
{
    const NOT_SET = 0;
    const _NEW = 1;
    const SCANNING = 2;
    const SCANNED = 3;
    const OVER_SCANNED = 4;
    const COMPLETED = 5;
    const DONE = 6;
    const CANCEL = 7;

    public static function getScannedList()
    {
        return [
            self::SCANNED,
            self::OVER_SCANNED,
        ];
    }
}
