<?php

namespace app\mobile\controller;

use app\common\BaseController;
use app\admin\logic\StockLogic;
use support\Request;
use support\Response;

class StockController extends BaseController
{
    public function checkList(Request $request): Response
    {
        $params = $request->get();
        $params['status'] = 1;
        $data = StockLogic::checkGetList($params);
        return $this->success($data);
    }

    public function checkItems(Request $request): Response
    {
        $id = $request->get('id');
        if (!$id) {
            return $this->fail('参数错误', 10000);
        }
        $data = StockLogic::checkGetItems((int)$id);
        return $this->success($data);
    }

    public function checkComplete(Request $request): Response
    {
        $data = $request->post();
        if (empty($data['id'])) {
            return $this->fail('参数错误', 10000);
        }
        $data['created_by'] = $request->userId;
        StockLogic::checkComplete($data);
        return $this->success(null, '盘点完成');
    }
}
