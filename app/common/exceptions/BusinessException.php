<?php

namespace app\common\exceptions;

use app\common\ResponseCode;
use Exception;

class BusinessException extends Exception
{
    public function __construct(string $message = '', int $code = 0)
    {
        parent::__construct($message ?: '业务异常', $code ?: ResponseCode::ERROR_BUSINESS->value);
    }
}
