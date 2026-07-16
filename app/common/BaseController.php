<?php

namespace app\common;

use support\Response;

class BaseController
{
    protected function success(mixed $data = null, string $message = 'success'): Response
    {
        return json([
            'code' => ResponseCode::SUCCESS->value,
            'message' => $message,
            'data' => $data,
        ]);
    }

    protected function fail(string $message = 'fail', int $code = 0, mixed $data = null): Response
    {
        return json([
            'code' => $code ?: ResponseCode::ERROR_BUSINESS->value,
            'message' => $message,
            'data' => $data,
        ]);
    }
}
