<?php

namespace app\admin\controller;

use app\admin\logic\StockCheckLogic;
use support\Request;
use support\Response;

class CheckTaskController
{
    public function list(Request $request): Response
    {
        $data = StockCheckLogic::getTaskList($request->get());
        return json(['code' => 0, 'message' => 'success', 'data' => $data]);
    }

    public function create(Request $request): Response
    {
        $data = $request->post();
        StockCheckLogic::createTask($data + ['created_by' => $request->userId]);
        return json(['code' => 0, 'message' => '任务创建成功']);
    }

    public function detail(Request $request): Response
    {
        $id = $request->get('id');
        if (!$id) {
            return json(['code' => 10000, 'message' => '参数错误']);
        }
        $data = StockCheckLogic::getTaskDetail((int)$id);
        return json(['code' => 0, 'message' => 'success', 'data' => $data]);
    }

    public function approve(Request $request): Response
    {
        $id = $request->post('id');
        if (!$id) {
            return json(['code' => 10000, 'message' => '参数错误']);
        }
        StockCheckLogic::approveTask((int)$id, $request->userId);
        return json(['code' => 0, 'message' => '审核通过，库存已调整']);
    }

    public function reject(Request $request): Response
    {
        $id = $request->post('id');
        if (!$id) {
            return json(['code' => 10000, 'message' => '参数错误']);
        }
        StockCheckLogic::rejectTask((int)$id, $request->userId);
        return json(['code' => 0, 'message' => '已驳回']);
    }
}
