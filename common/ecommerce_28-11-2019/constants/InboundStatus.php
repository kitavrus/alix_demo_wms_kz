<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 24.07.2019
 * Time: 19:59
 */

namespace common\ecommerce\constants;


class InboundStatus
{
    /*
 * @var status
 * */
    const NOT_SET = 0;// Статус не определен

    const _NEW = 1;
    const SCANNING = 2; //  СКАНИРУЕТСЯ
    const SCANNED = 3;  //  ОТСКАНИРОВАН
    const PLACED = 4;  //  РАЗМЕЩЕН
    const DONE = 5;    // ОТПРАВЛЕН ПО АПИ
    const CANCEL = 6;   // ОТМЕНЕН

    public static function getNewAndInProcessOrder() {
        return [
            self:: _NEW,
            self::SCANNING,
        ];
    }
}