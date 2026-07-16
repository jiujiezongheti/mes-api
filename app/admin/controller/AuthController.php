<?php

namespace app\admin\controller;

use app\common\BaseController;
use support\Request;
use support\Response;

class AuthController extends BaseController
{
    public function login(Request $request): Response
    {
        $data = $request->post();

        return $this->success(['token' => 'placeholder_token']);
    }
}
