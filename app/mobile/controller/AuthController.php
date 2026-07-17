<?php

namespace app\mobile\controller;

use app\common\BaseController;
use app\mobile\logic\AuthLogic;
use app\mobile\validate\AuthValidate;
use support\Request;
use support\Response;

class AuthController extends BaseController
{
    public function login(Request $request): Response
    {
        $data = $request->post();
        AuthValidate::login($data);

        $result = AuthLogic::login($data['username'], $data['password']);

        return $this->success($result);
    }

    public function refresh(Request $request): Response
    {
        $authorization = $request->header('Authorization', '');
        $token = str_replace('Bearer ', '', $authorization);

        $result = AuthLogic::refresh($token);

        return $this->success($result);
    }

    public function me(Request $request): Response
    {
        $userInfo = AuthLogic::getUserInfo($request->userId);

        return $this->success($userInfo);
    }
}
