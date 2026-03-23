<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 24.07.2019
 * Time: 19:59
 */

namespace common\ecommerce\constants;


class StockAPIStatus
{
    const NO = 0; // Не отправляли по АПИ
    const YES = 1; // Отправили по апи
    const ERROR = 2; // Отправили по апи с ошибкой

    public static function all()
    {
        return [
            self::NO,
            self::YES,
            self::ERROR,
        ];
    }
}