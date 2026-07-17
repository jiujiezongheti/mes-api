<?php

namespace app\admin\logic;

use app\common\model\Permission;

class PermissionLogic
{
    public static function getTree(): array
    {
        $all = Permission::orderBy('sort')->orderBy('id')->get()->toArray();
        return self::buildTree($all, null);
    }

    public static function getAll(): array
    {
        return Permission::orderBy('sort')->orderBy('id')->get()->toArray();
    }

    private static function buildTree(array $items, $parentId): array
    {
        $tree = [];
        foreach ($items as $item) {
            if ($item['parent_id'] == $parentId) {
                $children = self::buildTree($items, $item['id']);
                if ($children) {
                    $item['children'] = $children;
                }
                $tree[] = $item;
            }
        }
        return $tree;
    }
}
