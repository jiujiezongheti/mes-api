<?php

namespace app\admin\controller;

use support\Request;
use support\Response;
use app\admin\logic\StockLogic;
use app\admin\validate\StockValidate;

class StockController
{
    public function inventoryList(Request $request): Response
    {
        $data = StockLogic::getInventoryList($request->get());
        return json(['code' => 0, 'message' => 'success', 'data' => $data]);
    }

    public function in(Request $request): Response
    {
        $data = $request->post();
        StockValidate::in($data);
        StockLogic::in($data + ['created_by' => $request->userId]);
        return json(['code' => 0, 'message' => '入库成功']);
    }

    public function out(Request $request): Response
    {
        $data = $request->post();
        StockValidate::out($data);
        StockLogic::out($data + ['created_by' => $request->userId]);
        return json(['code' => 0, 'message' => '出库成功']);
    }

    public function recordList(Request $request): Response
    {
        $data = StockLogic::getRecordList($request->get());
        return json(['code' => 0, 'message' => 'success', 'data' => $data]);
    }

    public function checkCreate(Request $request): Response
    {
        $data = $request->post();
        StockValidate::checkCreate($data);
        StockLogic::checkCreate($data + ['created_by' => $request->userId]);
        return json(['code' => 0, 'message' => '盘点单创建成功']);
    }

    public function checkGetItems(Request $request): Response
    {
        $id = $request->get('id');
        if (!$id) {
            return json(['code' => 10000, 'message' => '参数错误']);
        }
        $data = StockLogic::checkGetItems((int)$id);
        return json(['code' => 0, 'message' => 'success', 'data' => $data]);
    }

    public function checkComplete(Request $request): Response
    {
        $data = $request->post();
        StockValidate::checkComplete($data);
        StockLogic::checkComplete($data + ['created_by' => $request->userId]);
        return json(['code' => 0, 'message' => '盘点完成']);
    }

    public function checkList(Request $request): Response
    {
        $data = StockLogic::checkGetList($request->get());
        return json(['code' => 0, 'message' => 'success', 'data' => $data]);
    }
}
