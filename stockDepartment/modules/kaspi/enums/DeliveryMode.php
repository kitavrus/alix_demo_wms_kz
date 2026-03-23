<?php

namespace stockDepartment\modules\kaspi\enums;

/*
 * Способ доставки
 * */
class DeliveryMode
{
    const DELIVERY_LOCAL = 'DELIVERY_LOCAL';                     // по городу — Kaspi Доставка или силами продавца
    const DELIVERY_PICKUP = 'DELIVERY_PICKUP';                   // самовывоз
    const DELIVERY_REGIONAL_TODOOR = 'DELIVERY_REGIONAL_TODOOR'; // доставка в Kaspi Postomat
    const DELIVERY_REGIONAL_PICKUP = 'DELIVERY_REGIONAL_PICKUP'; // доставка по области до склада самовывозом

    /**
     * Проверяет, валидный ли способ доставки
     * @param string $mode
     * @return bool
     */
    public static function isValid($mode)
    {
        return in_array($mode, [
            self::DELIVERY_LOCAL,
            self::DELIVERY_PICKUP,
            self::DELIVERY_REGIONAL_TODOOR,
            self::DELIVERY_REGIONAL_PICKUP,
        ], true);
    }
}

