<?php

namespace app\admin\controller;

use app\common\BaseController;
use support\Request;
use support\Response;

class IndexController extends BaseController
{
    public function dashboard(Request $request): Response
    {
        return $this->success([
            'orderCount' => 0,
            'deviceStatus' => [],
        ]);
    }
}
