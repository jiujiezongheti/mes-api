<?php

namespace app\admin\controller;

use app\common\BaseController;
use app\admin\logic\MaterialLogic;
use app\admin\validate\MaterialValidate;
use support\Request;
use support\Response;

class MaterialController extends BaseController
{
    public function list(Request $request): Response
    {
        $result = MaterialLogic::getList($request->get());
        return $this->success($result);
    }

    public function detail(Request $request): Response
    {
        $id = (int)$request->get('id');
        $material = \app\common\model\Material::find($id);
        if (!$material) {
            return $this->fail('物料不存在');
        }
        return $this->success($material);
    }

    public function create(Request $request): Response
    {
        $data = $request->post();
        MaterialValidate::create($data);
        $data['created_by'] = $request->userId;
        MaterialLogic::create($data);
        return $this->success(null, '创建成功');
    }

    public function update(Request $request): Response
    {
        $data = $request->post();
        MaterialValidate::update($data);
        MaterialLogic::update($data);
        return $this->success(null, '修改成功');
    }

    public function delete(Request $request): Response
    {
        $id = (int)$request->post('id');
        MaterialLogic::delete($id);
        return $this->success(null, '删除成功');
    }

    public function export(Request $request): Response
    {
        $ids = $request->get('ids');
        $ids = $ids ? array_map('intval', explode(',', $ids)) : null;
        $filePath = MaterialLogic::export($ids);
        return response()->download($filePath, '物料档案.xlsx');
    }

    public function import(Request $request): Response
    {
        $file = $request->file('file');
        if (!$file) {
            return $this->fail('请上传文件');
        }
        $filePath = $file->getPath() . '/' . $file->getFilename();
        $result = MaterialLogic::import($filePath);
        return $this->success($result, '导入成功');
    }
}
