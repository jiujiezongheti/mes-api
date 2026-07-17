<?php

namespace app\middleware;

use app\common\exceptions\BusinessException;
use app\common\utils\JwtUtil;
use Webman\MiddlewareInterface;
use Webman\Http\Request;
use Webman\Http\Response;

class MobileAuthMiddleware implements MiddlewareInterface
{
    public function process(Request $request, callable $handler): Response
    {
        $authorization = $request->header('Authorization', '');

        if (!$authorization) {
            throw new BusinessException('请先登录', 20000);
        }

        $token = str_replace('Bearer ', '', $authorization);

        $decoded = JwtUtil::verify($token, 'mobile');
        $request->userId = $decoded->user_id;

        return $handler($request);
    }
}
