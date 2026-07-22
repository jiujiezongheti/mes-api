<?php

namespace app\admin\controller;

use support\Request;
use support\Response;
use app\admin\logic\WarehouseLogic;
use app\admin\validate\WarehouseValidate;

class WarehouseController
{
    public function list(Request $request): Response
    {
        $data = WarehouseLogic::getList($request->get());
        return json(['code' => 0, 'message' => 'success', 'data' => $data]);
    }

    public function all(Request $request): Response
    {
        $data = WarehouseLogic::all();
        return json(['code' => 0, 'message' => 'success', 'data' => $data]);
    }

    public function create(Request $request): Response
    {
        $data = $request->post();
        WarehouseValidate::create($data);
        WarehouseLogic::create($data + ['created_by' => $request->userId]);
        return json(['code' => 0, 'message' => '创建成功']);
    }

    public function update(Request $request): Response
    {
        $data = $request->post();
        WarehouseValidate::update($data);
        WarehouseLogic::update($data);
        return json(['code' => 0, 'message' => '修改成功']);
    }

    public function delete(Request $request): Response
    {
        $id = $request->post('id');
        if (!$id) {
            return json(['code' => 10000, 'message' => '参数错误']);
        }
        WarehouseLogic::delete((int)$id);
        return json(['code' => 0, 'message' => '删除成功']);
    }
}
