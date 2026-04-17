<?php

namespace stockDepartment\modules\kaspi\enums;

class OrderStatus
{
    const ORDER_APPROVED_BY_BANK = 'APPROVED_BY_BANK';
    const ORDER_ACCEPTED_BY_MERCHANT = 'ACCEPTED_BY_MERCHANT';
    const ORDER_ASSEMBLE = 'ASSEMBLE';
    const ORDER_KASPI_DELIVERY = 'KASPI_DELIVERY';
    const ORDER_COMPLETED = 'COMPLETED';
    const ORDER_CANCELLED = 'CANCELLED';
    const ORDER_CANCELLING = 'CANCELLING';
    const ORDER_ARRIVED = 'ARRIVED';
    const ORDER_ARRIVED_BACKWARD = 'ARRIVED_BACKWARD';
    const ORDER_KASPI_DELIVERY_RETURN_REQUESTED = 'KASPI_DELIVERY_RETURN_REQUESTED';
    const ORDER_RETURNED = 'RETURNED';

    public static function isValid($status)
    {
        return in_array($status, [
            self::ORDER_APPROVED_BY_BANK,
            self::ORDER_ACCEPTED_BY_MERCHANT,
            self::ORDER_ASSEMBLE,
            self::ORDER_KASPI_DELIVERY,
            self::ORDER_COMPLETED,
            self::ORDER_CANCELLED,
            self::ORDER_CANCELLING,
            self::ORDER_ARRIVED,
            self::ORDER_ARRIVED_BACKWARD,
            self::ORDER_KASPI_DELIVERY_RETURN_REQUESTED,
            self::ORDER_RETURNED,
        ], true);
    }
}

