<?php

namespace stockDepartment\modules\alix\controllers\ecommerce\api\v1\inbound\dto\add_order;

/**
 * @property string $orderNumber
 * @property integer $clientId
 * @property integer $expectedQty
 * @property array $items
 */
class AddOrderRequestDTO
{
    public $orderNumber = "";
    public $comment = "";
    public $clientId = 103;
    public $uuid_1c = "";
    public $from_location = 0;
    public $expectedQty = 0;
    public $items = [];
}
