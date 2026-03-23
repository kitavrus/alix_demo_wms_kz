<?php

namespace app\modules\intermode\controllers\common\notify\b2b;

class NewOutboundOrderMsgDTO
{
	public $orderNumber =  '';
	public $expectedProductQty = '';
	public $storeName =  '';
	public $comment =  '';

	/**
	 * @param string $orderNumber
	 * @param string $expectedProductQty
	 * @param string $comment
	 * @param string $storeName
	 */
	public function __construct($orderNumber, $expectedProductQty,$storeName, $comment)
	{
		$this->orderNumber = $orderNumber;
		$this->expectedProductQty = $expectedProductQty;
		$this->storeName = $storeName;
		$this->comment = $comment;
	}
}