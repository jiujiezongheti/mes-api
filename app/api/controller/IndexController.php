<?php

namespace app\api\controller;

use app\common\BaseController;
use support\Request;
use support\Response;

class IndexController extends BaseController
{
    public function webhook(Request $request): Response
    {
        return $this->success();
    }
}
