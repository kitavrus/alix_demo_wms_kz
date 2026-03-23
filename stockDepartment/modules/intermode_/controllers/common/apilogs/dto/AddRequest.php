<?php

namespace app\modules\intermode\controllers\common\apilogs\dto;

/**
 * This is the model class for table "AddResponse".
 *
 * @property int $our_order_id Our in/out/return id
 * @property string $their_order_number Their in/out/return number
 * @property string $method_name Method name
 * @property string $order_type in/out/return b2b or b2c
 * @property string $request_data Request data
 * @property string $status Status
 */
class AddRequest
{
	var $our_order_id;
	var $their_order_number;
	var $method_name;
	var $order_type;
	var $request_data;
	var $request_status;
}