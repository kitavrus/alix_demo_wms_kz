<?php

namespace stockDepartment\modules\intermode\controllers\common\apilogs\dto;

/**
 * This is the model class for table "AddResponse".
 *
 * @property int $id  id
 * @property string $response_data Response data
 * @property string $response_code Response code
 * @property string $response_message Response error message
 */
class AddResponse
{
	var $id;
	var $response_data;
	var $response_code;
	var $response_message;
}