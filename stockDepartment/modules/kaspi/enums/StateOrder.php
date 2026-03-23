<?php

namespace stockDepartment\modules\kaspi\enums;

/*
 * Состояние заказа
 * */
class StateOrder
{
    const STATE_NEW = 'NEW';                       // новый
    const STATE_SIGN_REQUIRED = 'SIGN_REQUIRED';   // нужно подписать документы
    const STATE_PICKUP = 'PICKUP';                 // самовывоз
    const STATE_DELIVERY = 'DELIVERY';             // ваша доставка
    const STATE_KASPI_DELIVERY = 'KASPI_DELIVERY'; // Kaspi Доставка
    const STATE_ARCHIVE = 'ARCHIVE';               // архивный

    /**
     * Проверяет, валидно ли состояние заказа
     * @param string $mode
     * @return bool
     */
    public static function isValid($mode)
    {
        return in_array($mode, [
            self::STATE_NEW,
            self::STATE_SIGN_REQUIRED,
            self::STATE_PICKUP,
            self::STATE_DELIVERY,
            self::STATE_KASPI_DELIVERY,
            self::STATE_ARCHIVE
        ], true);
    }
}

