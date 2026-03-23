<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 15.11.2017
 * Time: 13:12
 */

namespace common\clientObject\constants;


class Constants
{
    const HYUNDAI_AUTO = 95;
    const HYUNDAI_TRUCK = 96;
    const SUBARU_AUTO = 97;
    public static function getCarPartClientIDs() {
        return [
            self::HYUNDAI_AUTO,
            self::HYUNDAI_TRUCK,
            self::SUBARU_AUTO,
        ];
    }
}