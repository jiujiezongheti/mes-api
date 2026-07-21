<?php

namespace app\admin\logic;

use app\common\model\MaterialCategory;
use app\common\exceptions\BusinessException;
use app\common\ResponseCode;
use app\common\utils\ExcelUtil;

class MaterialCategoryLogic
{
    public static function getList(array $params): array
    {
        $query = MaterialCategory::query();

        if (!empty($params['name'])) {
            $query->where('name', 'like', "%{$params['name']}%");
        }
        if (!empty($params['code'])) {
            $query->where('code', 'like', "%{$params['code']}%");
        }
        if (isset($params['status']) && $params['status'] !== '') {
            $query->where('status', (int)$params['status']);
        }

        $page = (int)($params['page'] ?? 1);
        $pageSize = (int)($params['pageSize'] ?? 20);

        $total = $query->count();
        $list = $query->orderBy('sort')->orderBy('id', 'desc')
            ->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->get();

        return [
            'list' => $list,
            'total' => $total,
            'page' => $page,
            'pageSize' => $pageSize,
        ];
    }

    public static function getAll(): array
    {
        return MaterialCategory::orderBy('sort')->orderBy('id')->get()->toArray();
    }

    public static function create(array $data): void
    {
        if (MaterialCategory::where('code', $data['code'])->exists()) {
            throw new BusinessException('分类编码已存在', ResponseCode::ERROR_PARAM->value);
        }

        $category = new MaterialCategory();
        $category->code = $data['code'];
        $category->name = $data['name'];
        $category->status = $data['status'] ?? 1;
        $category->sort = $data['sort'] ?? 0;
        $category->remark = $data['remark'] ?? null;
        $category->created_by = $data['created_by'] ?? null;
        $category->save();
    }

    public static function update(array $data): void
    {
        $category = MaterialCategory::find($data['id']);
        if (!$category) {
            throw new BusinessException('分类不存在', ResponseCode::ERROR_PARAM->value);
        }

        if (isset($data['code']) && $data['code'] !== $category->code) {
            if (MaterialCategory::where('code', $data['code'])->exists()) {
                throw new BusinessException('分类编码已存在', ResponseCode::ERROR_PARAM->value);
            }
            $category->code = $data['code'];
        }

        if (isset($data['name'])) $category->name = $data['name'];
        if (isset($data['status'])) $category->status = (int)$data['status'];
        if (isset($data['sort'])) $category->sort = (int)$data['sort'];
        if (isset($data['remark'])) $category->remark = $data['remark'];
        $category->save();
    }

    public static function delete(int $id): void
    {
        $category = MaterialCategory::find($id);
        if (!$category) {
            throw new BusinessException('分类不存在', ResponseCode::ERROR_PARAM->value);
        }

        if ($category->materials()->exists()) {
            throw new BusinessException('该分类下有物料，无法删除', ResponseCode::ERROR_PARAM->value);
        }

        $category->delete();
    }

    public static function export(?array $ids = null): string
    {
        $query = MaterialCategory::query();
        if ($ids !== null) {
            $query->whereIn('id', $ids);
        }
        $categories = $query->orderBy('id')->get();

        $headers = ['分类编码', '分类名称', '排序', '状态', '备注'];
        $rows = $categories->map(function ($c) {
            return [
                $c->code ?? '',
                $c->name,
                $c->sort,
                $c->status ? '启用' : '禁用',
                $c->remark ?? '',
            ];
        })->toArray();

        return ExcelUtil::export('物料分类', $headers, $rows);
    }

    public static function import(string $filePath): array
    {
        $data = ExcelUtil::import($filePath);
        $imported = 0;
        $errors = [];

        foreach ($data as $i => $row) {
            $line = $i + 2;
            $code = $row['分类编码'] ?? '';
            $name = $row['分类名称'] ?? '';
            if (empty($code) || empty($name)) {
                $errors[] = "第{$line}行：分类编码和分类名称不能为空";
                continue;
            }
            if (MaterialCategory::where('code', $code)->exists()) {
                $errors[] = "第{$line}行：分类编码 '{$code}' 已存在";
                continue;
            }

            $category = new MaterialCategory();
            $category->code = $code;
            $category->name = $name;
            $category->sort = (int)($row['排序'] ?? 0);
            $category->status = ($row['状态'] ?? '启用') === '启用' ? 1 : 0;
            $category->remark = $row['备注'] ?? null;
            $category->save();
            $imported++;
        }

        return ['imported' => $imported, 'errors' => $errors];
    }
}
