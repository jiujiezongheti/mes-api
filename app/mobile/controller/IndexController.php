<?php

namespace app\mobile\controller;

use app\common\BaseController;
use support\Request;
use support\Response;

class IndexController extends BaseController
{
    public function dashboard(Request $request): Response
    {
        return $this->success([
            'taskCount' => 0,
            'noticeList' => [],
        ]);
    }
}
