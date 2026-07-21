<?php

namespace app\admin\controller;

use app\common\BaseController;
use app\admin\logic\MaterialCategoryLogic;
use app\admin\validate\MaterialCategoryValidate;
use support\Request;
use support\Response;

class MaterialCategoryController extends BaseController
{
    public function list(Request $request): Response
    {
        $result = MaterialCategoryLogic::getList($request->get());
        return $this->success($result);
    }

    public function all(Request $request): Response
    {
        $result = MaterialCategoryLogic::getAll();
        return $this->success($result);
    }

    public function create(Request $request): Response
    {
        $data = $request->post();
        MaterialCategoryValidate::create($data);
        $data['created_by'] = $request->userId;
        MaterialCategoryLogic::create($data);
        return $this->success(null, '创建成功');
    }

    public function update(Request $request): Response
    {
        $data = $request->post();
        MaterialCategoryValidate::update($data);
        MaterialCategoryLogic::update($data);
        return $this->success(null, '修改成功');
    }

    public function delete(Request $request): Response
    {
        $id = (int)$request->post('id');
        MaterialCategoryLogic::delete($id);
        return $this->success(null, '删除成功');
    }

    public function export(Request $request): Response
    {
        $ids = $request->get('ids');
        $ids = $ids ? array_map('intval', explode(',', $ids)) : null;
        $filePath = MaterialCategoryLogic::export($ids);
        return response()->download($filePath, '物料分类.xlsx');
    }

    public function import(Request $request): Response
    {
        $file = $request->file('file');
        if (!$file) {
            return $this->fail('请上传文件');
        }
        $filePath = $file->getPath() . '/' . $file->getFilename();
        $result = MaterialCategoryLogic::import($filePath);
        return $this->success($result, '导入成功');
    }
}
