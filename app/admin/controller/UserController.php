<?php

namespace app\admin\controller;

use app\common\BaseController;
use app\admin\logic\UserLogic;
use support\Request;
use support\Response;

class UserController extends BaseController
{
    public function list(Request $request): Response
    {
        $result = UserLogic::getList($request->get());
        return $this->success($result);
    }

    public function create(Request $request): Response
    {
        $data = $request->post();
        $data['created_by'] = $request->userId;
        UserLogic::create($data);
        return $this->success(null, '创建成功');
    }

    public function update(Request $request): Response
    {
        $id = $request->post('id');
        UserLogic::update($id, $request->post());
        return $this->success(null, '修改成功');
    }

    public function delete(Request $request): Response
    {
        $id = $request->post('id');
        UserLogic::delete($id);
        return $this->success(null, '删除成功');
    }

    public function export(Request $request): Response
    {
        $ids = $request->get('ids');
        $ids = $ids ? array_map('intval', explode(',', $ids)) : null;
        $filePath = UserLogic::export($ids);
        return response()->download($filePath, '用户列表.xlsx');
    }

    public function import(Request $request): Response
    {
        $file = $request->file('file');
        if (!$file) {
            return $this->fail('请上传文件');
        }
        $filePath = $file->getPath() . '/' . $file->getFilename();
        $result = UserLogic::import($filePath);
        return $this->success($result, '导入成功');
    }
}
