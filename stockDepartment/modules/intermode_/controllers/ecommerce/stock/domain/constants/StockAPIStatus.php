<?php
namespace app\modules\intermode\controllers\ecommerce\stock\domain\constants;


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