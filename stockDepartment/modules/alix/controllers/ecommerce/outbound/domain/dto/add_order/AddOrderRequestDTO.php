<?php

namespace stockDepartment\modules\alix\controllers\ecommerce\outbound\domain\dto\add_order;

/**
 *
 * @property string $orderNumber
 * @property integer $clientId
 * @property integer $expectedQty
 * @property array $items
 */
class AddOrderRequestDTO
{
	public $orderNumber = "";
	public $clientId = 103;
	public $expectedQty = 0;
	public $shipmentSource = "CRM";
	public $items = [];
	
    public $firstName = "";
    public $lastName = "";
    public $customerName = "";
    public $email = "";
    public $phoneMobile = "";
    public $country = "";
    public $region = "";
    public $city = "";
    public $zipCode = "";
    public $street = "";
    public $paidPrice = 0;
}
