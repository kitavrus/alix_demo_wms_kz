<?php

namespace app\modules\intermode\controllers\api\v1\outbound\dto\add_order;

class ValidateDTO
{
	public $is_error = false;
	public $message = "";

	/**
	 * @param string $message
	 * @return $this
	 */
	public function withError($message)
	{
		$this->is_error = true;
		$this->message = $message;
		return $this;
	}

	/**
	 * @param string $message
	 * @return $this
	 */
	public function withOutError($message)
	{
		$this->is_error = false;
		$this->message = $message;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isInvalid()
	{
		return $this->is_error;
	}

	/**
	 * @return string
	 */
	public function getMessage()
	{
		return $this->message;
	}
}