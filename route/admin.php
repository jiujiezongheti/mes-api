<?php

use Webman\Route;

Route::group('/admin', function () {
    Route::post('/auth/login', [app\admin\controller\AuthController::class, 'login']);
    Route::post('/auth/refresh', [app\admin\controller\AuthController::class, 'refresh']);
    Route::group('/', function () {
        Route::get('/auth/me', [app\admin\controller\AuthController::class, 'me']);
        Route::post('/auth/password', [app\admin\controller\AuthController::class, 'password']);

        Route::get('/dashboard', [app\admin\controller\IndexController::class, 'dashboard']);

        Route::get('/user/list', [app\admin\controller\UserController::class, 'list'])->setParams(['permission' => 'admin:user:list']);
        Route::post('/user/create', [app\admin\controller\UserController::class, 'create'])->setParams(['permission' => 'admin:user:create']);
        Route::post('/user/update', [app\admin\controller\UserController::class, 'update'])->setParams(['permission' => 'admin:user:edit']);
        Route::post('/user/delete', [app\admin\controller\UserController::class, 'delete'])->setParams(['permission' => 'admin:user:delete']);

        Route::get('/role/list', [app\admin\controller\RoleController::class, 'list'])->setParams(['permission' => 'admin:role:list']);
        Route::get('/role/all', [app\admin\controller\RoleController::class, 'all'])->setParams(['permission' => 'admin:role:list']);
        Route::post('/role/create', [app\admin\controller\RoleController::class, 'create'])->setParams(['permission' => 'admin:role:create']);
        Route::post('/role/update', [app\admin\controller\RoleController::class, 'update'])->setParams(['permission' => 'admin:role:edit']);
        Route::post('/role/delete', [app\admin\controller\RoleController::class, 'delete'])->setParams(['permission' => 'admin:role:delete']);
        Route::get('/role/permission-ids', [app\admin\controller\RoleController::class, 'permissionIds'])->setParams(['permission' => 'admin:role:edit']);
        Route::post('/role/bind-permissions', [app\admin\controller\RoleController::class, 'bindPermissions'])->setParams(['permission' => 'admin:role:edit']);

        Route::get('/permission/tree', [app\admin\controller\PermissionController::class, 'tree'])->setParams(['permission' => 'admin:role:edit']);

        Route::get('/user/export', [app\admin\controller\UserController::class, 'export'])->setParams(['permission' => 'admin:user:export']);
        Route::post('/user/import', [app\admin\controller\UserController::class, 'import'])->setParams(['permission' => 'admin:user:import']);

        Route::get('/role/export', [app\admin\controller\RoleController::class, 'export'])->setParams(['permission' => 'admin:role:export']);
        Route::post('/role/import', [app\admin\controller\RoleController::class, 'import'])->setParams(['permission' => 'admin:role:import']);
    })->middleware([app\middleware\AdminAuthMiddleware::class, app\middleware\PermissionMiddleware::class]);
});
