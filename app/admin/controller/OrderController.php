<?php

namespace app\admin\controller;

use support\Request;
use support\Response;
use app\admin\logic\OrderLogic;
use app\admin\validate\OrderValidate;

class OrderController
{
    public function list(Request $request): Response
    {
        $data = OrderLogic::getList($request->get());
        return json(['code' => 0, 'message' => 'success', 'data' => $data]);
    }

    public function detail(Request $request): Response
    {
        $id = $request->get('id');
        if (!$id) {
            return json(['code' => 10000, 'message' => '参数错误']);
        }
        $data = OrderLogic::detail((int)$id);
        return json(['code' => 0, 'message' => 'success', 'data' => $data]);
    }

    public function create(Request $request): Response
    {
        $data = $request->post();
        OrderValidate::create($data);
        OrderLogic::create($data + ['created_by' => $request->userId]);
        return json(['code' => 0, 'message' => '创建成功']);
    }

    public function update(Request $request): Response
    {
        $data = $request->post();
        OrderValidate::update($data);
        OrderLogic::update($data);
        return json(['code' => 0, 'message' => '修改成功']);
    }

    public function delete(Request $request): Response
    {
        $id = $request->post('id');
        if (!$id) {
            return json(['code' => 10000, 'message' => '参数错误']);
        }
        OrderLogic::delete((int)$id);
        return json(['code' => 0, 'message' => '删除成功']);
    }

    public function status(Request $request): Response
    {
        $data = $request->post();
        OrderValidate::status($data);
        OrderLogic::status($data);
        return json(['code' => 0, 'message' => '状态更新成功']);
    }

    public function materialsByBom(Request $request): Response
    {
        $bomId = $request->get('bom_id');
        $quantity = (float)($request->get('quantity', 1));
        if (!$bomId) {
            return json(['code' => 10000, 'message' => '请选择BOM']);
        }
        $data = OrderLogic::getMaterialsByBom((int)$bomId, $quantity);
        return json(['code' => 0, 'message' => 'success', 'data' => $data]);
    }
}
