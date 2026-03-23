<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 24.07.2019
 * Time: 19:59
 */

namespace common\ecommerce\constants;


class OutboundListStatus
{
    const NO = 0; // Не напечатали
    const PRINTED = 1; // Напечатали

    public static function all()
    {
        return [
            self::NO,
            self::PRINTED,
        ];
    }
}