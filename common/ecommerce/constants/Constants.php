<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 15.11.2017
 * Time: 13:12
 */

namespace common\ecommerce\constants;


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

    public static function getHyundaiAutoID() { return  self::HYUNDAI_AUTO; }
    public static function getHyundaiTruckID() { return  self::HYUNDAI_TRUCK; }
    public static function getSubaruAutoID() { return  self::SUBARU_AUTO; }
}