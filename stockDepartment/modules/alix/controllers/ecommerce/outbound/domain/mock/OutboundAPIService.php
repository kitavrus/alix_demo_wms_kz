<?php

namespace stockDepartment\modules\alix\controllers\ecommerce\outbound\domain\mock;

use stockDepartment\modules\alix\controllers\ecommerce\outbound\domain\constants\OutboundAPIStatus;
use stockDepartment\modules\alix\controllers\ecommerce\outbound\domain\dto\add_order\AddOrderItemRequestDTO;
use stockDepartment\modules\alix\controllers\ecommerce\outbound\domain\dto\add_order\AddOrderRequestDTO;
use stockDepartment\modules\intermode\controllers\ecommerce\outbound\domain\dto\add_order\AddOrderResponseDTO;
use stockDepartment\modules\intermode\controllers\ecommerce\outbound\domain\entities\EcommerceOutbound;
use stockDepartment\modules\intermode\controllers\ecommerce\outbound\domain\entities\EcommerceOutboundItem;
use stockDepartment\modules\intermode\controllers\ecommerce\outbound\domain\validation\ValidationOutbound;
use yii\httpclient\Client as HttpClient;
use yii\httpclient\Request;

class OutboundAPIService
{
	public function __construct()
	{
	}
	/**
	 * @return Request request instance.
	 */
	private function createRequest() {
		return new \stdClass();
	}
	/**
	 * @param AddOrderResponseDTO $data
	 * @return boolean
	 */
	public function sendStatusOutOfStock($data)
	{
		return $this->sendStatus($data,OutboundAPIStatus::OUT_OF_STOCK);
	}


	/**
	 * @param AddOrderResponseDTO $data
	 * @return boolean
	 */
	public function sendStatusPickedPartWarehouse($data)
	{
		return $this->sendStatus($data,OutboundAPIStatus::PICKED_PART_WAREHOUSE);
	}

	/**
	 * @param AddOrderResponseDTO $data
	 * @return boolean
	 */
	public function sendStatusPickedWarehouse($data)
	{
		return $this->sendStatus($data,OutboundAPIStatus::PICKED_WAREHOUSE);
	}

	/**
	 * @param AddOrderResponseDTO $data
	 * @return boolean
	 */
	public function sendStatusWainingPicking($data)
	{
		return $this->sendStatus($data,OutboundAPIStatus::WAINING_PICKING);
	}

	/**
	 * @param AddOrderResponseDTO $data
	 * @return boolean
	 */
	public function sendStatusInWork($data)
	{
		return $this->sendStatus($data,OutboundAPIStatus::IN_WORK);
	}

	/**
	 * @param AddOrderResponseDTO $data
	 * @return boolean
	 */
	public function sendStatusNew($data)
	{
		return $this->sendStatus($data,OutboundAPIStatus::_NEW);
	}

	/**
	 * @param AddOrderResponseDTO $data
	 * @param string $status
	 * @return boolean
	 */
	private function sendStatus($data,$status)
	{
		file_put_contents("sendOutboundStatus-MOCK.log",date(DATE_ISO8601)."\n"."Request:"."\n".print_r($data,true)."\n".print_r($status,true)."\n",FILE_APPEND);
		return true;
	}
}