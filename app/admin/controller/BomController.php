<?php

namespace app\admin\controller;

use app\common\BaseController;
use app\admin\logic\BomLogic;
use app\admin\validate\BomValidate;
use support\Request;
use support\Response;

class BomController extends BaseController
{
    public function list(Request $request): Response
    {
        $result = BomLogic::getList($request->get());
        return $this->success($result);
    }

    public function detail(Request $request): Response
    {
        $id = (int)$request->get('id');
        $result = BomLogic::detail($id);
        return $this->success($result);
    }

    public function create(Request $request): Response
    {
        $data = $request->post();
        BomValidate::create($data);
        $data['created_by'] = $request->userId;
        BomLogic::create($data);
        return $this->success(null, '创建成功');
    }

    public function update(Request $request): Response
    {
        $data = $request->post();
        BomValidate::update($data);
        BomLogic::update($data);
        return $this->success(null, '修改成功');
    }

    public function delete(Request $request): Response
    {
        $id = (int)$request->post('id');
        BomLogic::delete($id);
        return $this->success(null, '删除成功');
    }

    public function export(Request $request): Response
    {
        $ids = $request->get('ids');
        $ids = $ids ? array_map('intval', explode(',', $ids)) : null;
        $filePath = BomLogic::export($ids);
        return response()->download($filePath, 'BOM数据.xlsx');
    }

    public function import(Request $request): Response
    {
        $file = $request->file('file');
        if (!$file) {
            return $this->fail('请上传文件');
        }
        $filePath = $file->getPath() . '/' . $file->getFilename();
        $result = BomLogic::import($filePath);
        return $this->success($result, '导入成功');
    }

    public function tree(Request $request): Response
    {
        $id = (int)$request->get('id');
        $result = BomLogic::tree($id);
        return $this->success($result);
    }

    public function whereUsed(Request $request): Response
    {
        $materialId = (int)$request->get('material_id');
        $result = BomLogic::whereUsed($materialId);
        return $this->success($result);
    }

    public function copy(Request $request): Response
    {
        $data = $request->post();
        BomValidate::copy($data);
        $materialId = !empty($data['material_id']) ? (int)$data['material_id'] : null;
        BomLogic::copy((int)$data['id'], $data['code'], $data['name'], $materialId);
        return $this->success(null, '复制成功');
    }
}
