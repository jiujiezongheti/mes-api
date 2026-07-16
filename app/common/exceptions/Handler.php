<?php

namespace app\common\exceptions;

use app\common\ResponseCode;
use Throwable;
use Webman\Http\Request;
use Webman\Http\Response;

class Handler extends \support\exception\Handler
{
    public function render(Request $request, Throwable $exception): Response
    {
        if ($exception instanceof BusinessException) {
            return json([
                'code' => $exception->getCode(),
                'message' => $exception->getMessage(),
                'data' => null,
            ]);
        }

        return json([
            'code' => ResponseCode::ERROR_SYSTEM->value,
            'message' => config('app.debug') ? $exception->getMessage() : '系统内部错误',
            'data' => null,
        ]);
    }
}
