<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 29.07.2017
 * Time: 9:16
 */

namespace common\ecommerce\defacto\outbound\dto;

/**
 * This is the model class for table "{{%user}}".
 *
 * @property string $orderId
 * @property string $orderNumber
 * @property string $courierCompany
 * @property string $cargoShipmentNo
 * @property integer $kg
 * @property string $trackingNumber
 * @property string $trackingUrl
 * @property string $referenceNumber
 */
class SendCargoDeliveryIn
{
	public $orderId;
	public $orderNumber;
	public $courierCompany;
	public $cargoShipmentNo;
	public $kg;
	public $trackingNumber;
	public $trackingUrl;
	public $referenceNumber;
}
