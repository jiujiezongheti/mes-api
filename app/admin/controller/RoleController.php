<?php

namespace app\admin\controller;

use app\common\BaseController;
use app\admin\logic\RoleLogic;
use support\Request;
use support\Response;

class RoleController extends BaseController
{
    public function list(Request $request): Response
    {
        $page = (int)$request->get('page', 1);
        $pageSize = (int)$request->get('pageSize', 20);
        $result = RoleLogic::getList($page, $pageSize, $request->get());
        return $this->success($result);
    }

    public function all(Request $request): Response
    {
        $result = RoleLogic::getAll();
        return $this->success($result);
    }

    public function create(Request $request): Response
    {
        $data = $request->post();
        $data['created_by'] = $request->userId;
        RoleLogic::create($data);
        return $this->success(null, '创建成功');
    }

    public function update(Request $request): Response
    {
        $id = $request->post('id');
        RoleLogic::update($id, $request->post());
        return $this->success(null, '修改成功');
    }

    public function delete(Request $request): Response
    {
        $id = $request->post('id');
        RoleLogic::delete($id);
        return $this->success(null, '删除成功');
    }

    public function permissionIds(Request $request): Response
    {
        $id = $request->get('id');
        $permissionIds = RoleLogic::getPermissionIds($id);
        return $this->success($permissionIds);
    }

    public function bindPermissions(Request $request): Response
    {
        $id = $request->post('id');
        $permissionIds = $request->post('permission_ids', []);
        RoleLogic::bindPermissions($id, $permissionIds);
        return $this->success(null, '绑定成功');
    }

    public function export(Request $request): Response
    {
        $ids = $request->get('ids');
        $ids = $ids ? array_map('intval', explode(',', $ids)) : null;
        $filePath = RoleLogic::export($ids);
        return response()->download($filePath, '角色列表.xlsx');
    }

    public function import(Request $request): Response
    {
        $file = $request->file('file');
        if (!$file) {
            return $this->fail('请上传文件');
        }
        $filePath = $file->getPath() . '/' . $file->getFilename();
        $result = RoleLogic::import($filePath);
        return $this->success($result, '导入成功');
    }
}
