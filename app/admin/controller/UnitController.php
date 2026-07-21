<?php

namespace app\admin\controller;

use app\common\BaseController;
use app\admin\logic\UnitLogic;
use app\admin\validate\UnitValidate;
use support\Request;
use support\Response;

class UnitController extends BaseController
{
    public function list(Request $request): Response
    {
        $result = UnitLogic::getList($request->get());
        return $this->success($result);
    }

    public function all(Request $request): Response
    {
        $result = UnitLogic::getAll();
        return $this->success($result);
    }

    public function create(Request $request): Response
    {
        $data = $request->post();
        UnitValidate::create($data);
        $data['created_by'] = $request->userId;
        UnitLogic::create($data);
        return $this->success(null, '创建成功');
    }

    public function update(Request $request): Response
    {
        $data = $request->post();
        UnitValidate::update($data);
        UnitLogic::update($data);
        return $this->success(null, '修改成功');
    }

    public function delete(Request $request): Response
    {
        $id = (int)$request->post('id');
        UnitLogic::delete($id);
        return $this->success(null, '删除成功');
    }

    public function export(Request $request): Response
    {
        $ids = $request->get('ids');
        $ids = $ids ? array_map('intval', explode(',', $ids)) : null;
        $filePath = UnitLogic::export($ids);
        return response()->download($filePath, '计量单位.xlsx');
    }

    public function import(Request $request): Response
    {
        $file = $request->file('file');
        if (!$file) {
            return $this->fail('请上传文件');
        }
        $filePath = $file->getPath() . '/' . $file->getFilename();
        $result = UnitLogic::import($filePath);
        return $this->success($result, '导入成功');
    }
}
