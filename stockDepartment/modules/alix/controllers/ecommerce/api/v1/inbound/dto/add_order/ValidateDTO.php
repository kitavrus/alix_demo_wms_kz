<?php

namespace stockDepartment\modules\alix\controllers\ecommerce\api\v1\inbound\dto\add_order;

class ValidateDTO
{
    public $is_error = false;
    public $message = "";

    public function withError($message)
    {
        $this->is_error = true;
        $this->message = $message;
        return $this;
    }

    public function withOutError($message)
    {
        $this->is_error = false;
        $this->message = $message;
        return $this;
    }

    public function isInvalid()
    {
        return $this->is_error;
    }

    public function getMessage()
    {
        return $this->message;
    }
}
