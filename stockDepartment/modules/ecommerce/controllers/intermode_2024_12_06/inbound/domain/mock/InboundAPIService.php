<?php

namespace app\modules\ecommerce\controllers\intermode\inbound\domain\mock;

use app\modules\ecommerce\controllers\intermode\inbound\domain\constants\InboundAPIStatus;
use app\modules\ecommerce\controllers\intermode\inbound\domain\dto\status_order\StatusOrderResponseDTO;
use yii\httpclient\Client as HttpClient;
use yii\httpclient\Request;

class InboundAPIService
{

	public function __construct()
	{
	}
	/**
	 * @return \stdClass request instance.
	 */
	private function createRequest() {
		return new \stdClass();
	}

	/**
	 * @param StatusOrderResponseDTO $data
	 * @return boolean
	 */
	public function sendStatusInWork($data)
	{
		return $this->sendStatus($data,InboundAPIStatus::IN_WORK);
	}

	/**
	 * @param StatusOrderResponseDTO $data
	 * @return boolean
	 */
	public function sendStatusCompletedWithStatus($data,$status = InboundAPIStatus::COMPLETED)
	{
		return $this->sendStatus($data,$status);
	}

	/**
	 * @param StatusOrderResponseDTO $data
	 * @param string $status
	 * @return boolean
	 */
	private function sendStatus($data,$status)
	{
		file_put_contents("sendInboundStatus-MOCK.log",date(DATE_ISO8601)."\n"."Request:"."\n".print_r($data,true)."\n".print_r($status,true)."\n",FILE_APPEND);
		return true;
	}
}