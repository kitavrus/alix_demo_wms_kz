<?php

namespace stockDepartment\modules\kaspi\dto;

/**
 * Полный ответ GET /orders: data → OrderDto[], meta, included (как в JSON:API Kaspi).
 */
class KaspiOrderListPayload
{
    /** @var OrderDto[] */
    public $orders = [];

    /** @var array pageCount, totalCount и т.д. */
    public $meta = [];

    /** @var array Сырой массив included из ответа API */
    public $included = [];
}
