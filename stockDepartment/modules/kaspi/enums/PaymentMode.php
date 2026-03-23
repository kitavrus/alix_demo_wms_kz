<?php

namespace stockDepartment\modules\kaspi\enums;

/*
 * Способ оплаты заказа
 * */
class PaymentMode
{
    const PAYMENT_MODE_PAY_WITH_CREDIT = 'PAY_WITH_CREDIT'; // Кредит на Покупки
    const PAYMENT_MODE_PREPAID = 'PREPAID';                 // Безналичная оплата

    /**
     * Проверяет, валидный ли способ оплаты
     * @param string $status
     * @return bool
     */
    public static function isValid($status) {
        return in_array($status, [
            self::PAYMENT_MODE_PAY_WITH_CREDIT,
            self::PAYMENT_MODE_PREPAID
        ], true);
    }
}

