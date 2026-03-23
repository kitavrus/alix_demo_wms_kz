<?php

namespace stockDepartment\modules\\controllers\common\notify\b2b;

class NewInboundOrderMsgDTO
{
	public $orderNumber =  '';
	public $expectedProductQty = '';
	public $comment =  '';

	/**
	 * @param string $orderNumber
	 * @param string $expectedProductQty
	 * @param string $comment
	 */
	public function __construct($orderNumber, $expectedProductQty, $comment)
	{
		$this->orderNumber = $orderNumber;
		$this->expectedProductQty = $expectedProductQty;
		$this->comment = $comment;
	}
}