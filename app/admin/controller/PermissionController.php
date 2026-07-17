<?php

namespace app\admin\controller;

use app\common\BaseController;
use app\admin\logic\PermissionLogic;
use support\Request;
use support\Response;

class PermissionController extends BaseController
{
    public function tree(Request $request): Response
    {
        $result = PermissionLogic::getTree();
        return $this->success($result);
    }

    public function all(Request $request): Response
    {
        $result = PermissionLogic::getAll();
        return $this->success($result);
    }
}
