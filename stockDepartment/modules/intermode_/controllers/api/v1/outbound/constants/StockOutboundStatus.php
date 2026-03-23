<?php

namespace app\modules\intermode\controllers\api\v1\outbound\constants;


class StockOutboundStatus
{
    /**
     * @var status
     * */
    const NOT_SET = 0;// Статус не определен
    const _NEW = 1;
    const SCANNING = 2; //  СКАНИРУЕТСЯ
    const SCANNED = 3;  //  ОТСКАНИРОВАН
    const OVER_SCANNED = 4;  //  ОТСКАНИРОВАН
    const COMPLETED = 5; //Нигде не используется!!!  ТОВАР ДОСТАВЛЕН ВСЕ В ПОРЯДКЕ
    const DONE = 6; //Нигде не используется!!!  ТОВАР ДОСТАВЛЕН ВСЕ В ПОРЯДКЕ
    const CANCEL = 7;   // ОТМЕНЕН

    public static function getScannedList() {
        return [
            self::SCANNED,
            self::OVER_SCANNED,
        ];
    }
}