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
    })->middleware([AdminAuthMiddleware::class, PermissionMiddleware::class]);
});
