<?php

use Webman\Route;
use app\admin\controller\AuthController;
use app\admin\controller\IndexController;
use app\admin\controller\UserController;
use app\admin\controller\RoleController;
use app\admin\controller\PermissionController;
use app\admin\controller\MaterialController;
use app\admin\controller\MaterialCategoryController;
use app\admin\controller\UnitController;
use app\admin\controller\BomController;
use app\admin\controller\OrderController;
use app\admin\controller\WarehouseController;
use app\admin\controller\StockController;
use app\admin\controller\CheckTaskController;
use app\middleware\AdminAuthMiddleware;
use app\middleware\PermissionMiddleware;

Route::options('/[{path:.*}]', function () {
    return response('', 204)
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With')
        ->withHeader('Access-Control-Max-Age', '86400');
});

Route::group('/admin', function () {
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/refresh', [AuthController::class, 'refresh']);

    Route::group('/auth', function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/password', [AuthController::class, 'password']);
    })->middleware([AdminAuthMiddleware::class]);

    Route::group('', function () {
        Route::get('/dashboard', [IndexController::class, 'dashboard']);

        Route::group('/user', function () {
            Route::get('/list', [UserController::class, 'list'])->setParams(['permission' => 'admin:user:list']);
            Route::post('/create', [UserController::class, 'create'])->setParams(['permission' => 'admin:user:create']);
            Route::post('/update', [UserController::class, 'update'])->setParams(['permission' => 'admin:user:edit']);
            Route::post('/delete', [UserController::class, 'delete'])->setParams(['permission' => 'admin:user:delete']);
            Route::get('/export', [UserController::class, 'export'])->setParams(['permission' => 'admin:user:export']);
            Route::post('/import', [UserController::class, 'import'])->setParams(['permission' => 'admin:user:import']);
        });

        Route::group('/role', function () {
            Route::get('/list', [RoleController::class, 'list'])->setParams(['permission' => 'admin:role:list']);
            Route::get('/all', [RoleController::class, 'all'])->setParams(['permission' => 'admin:role:list']);
            Route::post('/create', [RoleController::class, 'create'])->setParams(['permission' => 'admin:role:create']);
            Route::post('/update', [RoleController::class, 'update'])->setParams(['permission' => 'admin:role:edit']);
            Route::post('/delete', [RoleController::class, 'delete'])->setParams(['permission' => 'admin:role:delete']);
            Route::get('/permission-ids', [RoleController::class, 'permissionIds'])->setParams(['permission' => 'admin:role:edit']);
            Route::post('/bind-permissions', [RoleController::class, 'bindPermissions'])->setParams(['permission' => 'admin:role:edit']);
            Route::get('/export', [RoleController::class, 'export'])->setParams(['permission' => 'admin:role:export']);
            Route::post('/import', [RoleController::class, 'import'])->setParams(['permission' => 'admin:role:import']);
        });

        Route::group('/permission', function () {
            Route::get('/tree', [PermissionController::class, 'tree'])->setParams(['permission' => 'admin:role:edit']);
        });

        Route::group('/material', function () {
            Route::get('/list', [MaterialController::class, 'list'])->setParams(['permission' => 'admin:material:list']);
            Route::get('/detail', [MaterialController::class, 'detail'])->setParams(['permission' => 'admin:material:list']);
            Route::post('/create', [MaterialController::class, 'create'])->setParams(['permission' => 'admin:material:create']);
            Route::post('/update', [MaterialController::class, 'update'])->setParams(['permission' => 'admin:material:edit']);
            Route::post('/delete', [MaterialController::class, 'delete'])->setParams(['permission' => 'admin:material:delete']);
            Route::get('/export', [MaterialController::class, 'export'])->setParams(['permission' => 'admin:material:export']);
            Route::post('/import', [MaterialController::class, 'import'])->setParams(['permission' => 'admin:material:import']);

            Route::group('/category', function () {
                Route::get('/all', [MaterialCategoryController::class, 'all'])->setParams(['permission' => 'admin:material:category']);
                Route::get('/list', [MaterialCategoryController::class, 'list'])->setParams(['permission' => 'admin:material:category']);
                Route::post('/create', [MaterialCategoryController::class, 'create'])->setParams(['permission' => 'admin:material:category:create']);
                Route::post('/update', [MaterialCategoryController::class, 'update'])->setParams(['permission' => 'admin:material:category:edit']);
                Route::post('/delete', [MaterialCategoryController::class, 'delete'])->setParams(['permission' => 'admin:material:category:delete']);
                Route::get('/export', [MaterialCategoryController::class, 'export'])->setParams(['permission' => 'admin:material:category:export']);
                Route::post('/import', [MaterialCategoryController::class, 'import'])->setParams(['permission' => 'admin:material:category:import']);
            });

            Route::group('/unit', function () {
                Route::get('/all', [UnitController::class, 'all'])->setParams(['permission' => 'admin:unit:list']);
                Route::get('/list', [UnitController::class, 'list'])->setParams(['permission' => 'admin:unit:list']);
                Route::post('/create', [UnitController::class, 'create'])->setParams(['permission' => 'admin:unit:create']);
                Route::post('/update', [UnitController::class, 'update'])->setParams(['permission' => 'admin:unit:edit']);
                Route::post('/delete', [UnitController::class, 'delete'])->setParams(['permission' => 'admin:unit:delete']);
                Route::get('/export', [UnitController::class, 'export'])->setParams(['permission' => 'admin:unit:export']);
                Route::post('/import', [UnitController::class, 'import'])->setParams(['permission' => 'admin:unit:import']);
            });
        });

        Route::group('/production', function () {
            Route::group('/bom', function () {
                Route::get('/list', [BomController::class, 'list'])->setParams(['permission' => 'admin:bom:list']);
                Route::get('/detail', [BomController::class, 'detail'])->setParams(['permission' => 'admin:bom:list']);
                Route::post('/create', [BomController::class, 'create'])->setParams(['permission' => 'admin:bom:create']);
                Route::post('/update', [BomController::class, 'update'])->setParams(['permission' => 'admin:bom:edit']);
                Route::post('/delete', [BomController::class, 'delete'])->setParams(['permission' => 'admin:bom:delete']);
                Route::get('/tree', [BomController::class, 'tree'])->setParams(['permission' => 'admin:bom:list']);
                Route::get('/where-used', [BomController::class, 'whereUsed'])->setParams(['permission' => 'admin:bom:list']);
                Route::post('/copy', [BomController::class, 'copy'])->setParams(['permission' => 'admin:bom:create']);
                Route::get('/export', [BomController::class, 'export'])->setParams(['permission' => 'admin:bom:export']);
                Route::post('/import', [BomController::class, 'import'])->setParams(['permission' => 'admin:bom:import']);
            });

            Route::group('/order', function () {
                Route::get('/list', [OrderController::class, 'list'])->setParams(['permission' => 'admin:order:list']);
                Route::get('/detail', [OrderController::class, 'detail'])->setParams(['permission' => 'admin:order:list']);
                Route::post('/create', [OrderController::class, 'create'])->setParams(['permission' => 'admin:order:create']);
                Route::post('/update', [OrderController::class, 'update'])->setParams(['permission' => 'admin:order:edit']);
                Route::post('/delete', [OrderController::class, 'delete'])->setParams(['permission' => 'admin:order:delete']);
                Route::post('/status', [OrderController::class, 'status'])->setParams(['permission' => 'admin:order:edit']);
                Route::get('/materials-by-bom', [OrderController::class, 'materialsByBom'])->setParams(['permission' => 'admin:order:create']);
            });
        });

        Route::group('/warehouse', function () {
            Route::get('/list', [WarehouseController::class, 'list'])->setParams(['permission' => 'admin:warehouse:list']);
            Route::get('/all', [WarehouseController::class, 'all'])->setParams(['permission' => 'admin:warehouse:list']);
            Route::post('/create', [WarehouseController::class, 'create'])->setParams(['permission' => 'admin:warehouse:create']);
            Route::post('/update', [WarehouseController::class, 'update'])->setParams(['permission' => 'admin:warehouse:edit']);
            Route::post('/delete', [WarehouseController::class, 'delete'])->setParams(['permission' => 'admin:warehouse:delete']);
        });

        Route::group('/stock', function () {
            Route::get('/inventory-list', [StockController::class, 'inventoryList'])->setParams(['permission' => 'admin:stock:list']);
            Route::post('/in', [StockController::class, 'in'])->setParams(['permission' => 'admin:stock:in']);
            Route::post('/out', [StockController::class, 'out'])->setParams(['permission' => 'admin:stock:out']);
            Route::get('/record-list', [StockController::class, 'recordList'])->setParams(['permission' => 'admin:stock:list']);

            Route::group('/check', function () {
                Route::get('/list', [StockController::class, 'checkList'])->setParams(['permission' => 'admin:stock:check']);
                Route::post('/create', [StockController::class, 'checkCreate'])->setParams(['permission' => 'admin:stock:check']);
                Route::get('/items', [StockController::class, 'checkGetItems'])->setParams(['permission' => 'admin:stock:check']);
                Route::post('/complete', [StockController::class, 'checkComplete'])->setParams(['permission' => 'admin:stock:check']);
            });
        });

        Route::group('/check-task', function () {
            Route::get('/list', [CheckTaskController::class, 'list'])->setParams(['permission' => 'admin:stock:check']);
            Route::post('/create', [CheckTaskController::class, 'create'])->setParams(['permission' => 'admin:stock:check']);
            Route::get('/detail', [CheckTaskController::class, 'detail'])->setParams(['permission' => 'admin:stock:check']);
            Route::post('/approve', [CheckTaskController::class, 'approve'])->setParams(['permission' => 'admin:stock:check']);
            Route::post('/reject', [CheckTaskController::class, 'reject'])->setParams(['permission' => 'admin:stock:check']);
        });
    })->middleware([AdminAuthMiddleware::class, PermissionMiddleware::class]);
});
