<?php

namespace app\middleware;

use app\common\exceptions\BusinessException;
use app\common\ResponseCode;
use app\admin\logic\AuthLogic;
use Webman\MiddlewareInterface;
use Webman\Http\Request;
use Webman\Http\Response;
use Webman\Route;

class PermissionMiddleware implements MiddlewareInterface
{
    public function process(Request $request, callable $handler): Response
    {
        $route = Route::getCurrentRoute();
        if (!$route) {
            return $handler($request);
        }

        $permissionCode = $route->param('permission');

        if (!$permissionCode) {
            return $handler($request);
        }

        if (!isset($request->userId)) {
            throw new BusinessException('请先登录', ResponseCode::ERROR_AUTH->value);
        }

        $permissions = AuthLogic::getUserPermissions($request->userId);

        if (!in_array($permissionCode, $permissions, true)) {
            throw new BusinessException('无权限访问', ResponseCode::ERROR_FORBIDDEN->value);
        }

        return $handler($request);
    }
}
