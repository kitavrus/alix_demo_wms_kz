<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 08.09.2017
 * Time: 19:32
 */

namespace common\modules\city\RouteDirection\constant;


use common\overloads\ArrayHelper;

class Constant
{
    const BASE_TYPE_ROUTE = 1; // Базовый
    const BASE_TYPE_CUSTOM = 2; // Пользовательский
    const BASE_TYPE_CITY = 3;
    const BASE_TYPE_COUNTRY = 4;
    const BASE_TYPE_CARDINAL_POINTS = 5; // Стороны света
    const BASE_TYPE_FOR_SEARCH = 6; // Для поиска

    /*
    * Return array data []
    * @return array BASE TYPE
    * */
    public static function getTypeArrayData() {
        return [
            self::BASE_TYPE_CUSTOM => 'Дополнительный',
            self::BASE_TYPE_ROUTE => 'Базовый',
            self::BASE_TYPE_CITY => 'Город',
            self::BASE_TYPE_COUNTRY => 'Страна',
            self::BASE_TYPE_CARDINAL_POINTS => 'Стороны света',
        ];
    }
}