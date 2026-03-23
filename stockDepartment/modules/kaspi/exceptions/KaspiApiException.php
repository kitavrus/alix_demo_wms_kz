<?php

namespace stockDepartment\modules\kaspi\exceptions;

/** HTTP не 2xx или сбой транспорта. */
class KaspiApiException extends \RuntimeException
{
    private $httpStatusCode;

    private $responseBody;

    public function __construct(
        $message = '',
        $code = 0,
        \Exception $previous = null,
        $httpStatusCode = null,
        $responseBody = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->httpStatusCode = $httpStatusCode;
        $this->responseBody = $responseBody;
    }

    public function getHttpStatusCode()
    {
        return $this->httpStatusCode;
    }

    public function getResponseBody()
    {
        return $this->responseBody;
    }
}
