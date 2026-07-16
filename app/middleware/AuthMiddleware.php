<?php

namespace app\middleware;

use app\common\exceptions\BusinessException;
use Webman\MiddlewareInterface;
use Webman\Http\Request;
use Webman\Http\Response;

class AuthMiddleware implements MiddlewareInterface
{
    public function process(Request $request, callable $handler): Response
    {
        $token = $request->header('Authorization', '');

        if (!$token) {
            throw new BusinessException('请先登录', 20000);
        }

        $request->userId = 1;

        return $handler($request);
    }
}
