<?php

namespace stockDepartment\modules\alix\controllers\common\notify\b2b;

class CompleteOutboundOrderMsgDTO
{
	public $orderNumber =  '';
	public $logMessage =  '';

	/**
	 * @param string $orderNumber
	 * @param string $logMessage
	 */
	public function __construct($orderNumber,$logMessage)
	{
		$this->orderNumber = $orderNumber;
		$this->logMessage = $logMessage;
	}
}