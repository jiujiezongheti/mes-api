<?php

namespace app\mobile\controller;

use app\common\BaseController;
use app\admin\logic\StockCheckLogic;
use app\common\model\Material;
use support\Request;
use support\Response;

class StockCheckController extends BaseController
{
    public function taskList(Request $request): Response
    {
        $params = $request->get();
        $data = StockCheckLogic::getTaskList($params);
        return $this->success($data);
    }

    public function taskDetail(Request $request): Response
    {
        $id = $request->get('id');
        if (!$id) {
            return $this->fail('参数错误', 10000);
        }
        $data = StockCheckLogic::getTaskDetail((int)$id);
        return $this->success($data);
    }

    public function recordCreate(Request $request): Response
    {
        $data = $request->post();
        if (empty($data['material_code']) && empty($data['material_id'])) {
            return $this->fail('请输入物料编码或扫码', 10000);
        }

        if (!empty($data['material_code'])) {
            $material = Material::where('code', $data['material_code'])->first();
            if (!$material) {
                return $this->fail('未找到该物料', 10000);
            }
            $data['material_id'] = $material->id;
        }

        $data['created_by'] = $request->userId;
        StockCheckLogic::createRecord($data);
        return $this->success(null, '录入成功');
    }

    public function materialByCode(Request $request): Response
    {
        $code = $request->get('code');
        if (!$code) {
            return $this->fail('参数错误', 10000);
        }
        $material = Material::where('code', $code)->first(['id', 'code', 'name', 'spec']);
        if (!$material) {
            return $this->fail('未找到该物料', 10000);
        }
        return $this->success($material);
    }

    public function taskComplete(Request $request): Response
    {
        $id = $request->post('id');
        if (!$id) {
            return $this->fail('参数错误', 10000);
        }
        StockCheckLogic::completeTask((int)$id, $request->userId);
        return $this->success(null, '已提交审核');
    }

    public function batchControl(Request $request): Response
    {
        return $this->success(['enabled' => false]);
    }
}
