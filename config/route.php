<?php

use Webman\Route;

Route::group('/admin', function () {
    Route::post('/auth/login', [app\admin\controller\AuthController::class, 'login']);
    Route::group('/', function () {
        Route::get('/dashboard', [app\admin\controller\IndexController::class, 'dashboard']);
    })->middleware([app\middleware\AuthMiddleware::class]);
});

Route::group('/mobile', function () {
    Route::post('/auth/login', [app\mobile\controller\AuthController::class, 'login']);
    Route::group('/', function () {
        Route::get('/dashboard', [app\mobile\controller\IndexController::class, 'dashboard']);
    })->middleware([app\middleware\AuthMiddleware::class]);
});

Route::group('/api', function () {
    Route::post('/webhook', [app\api\controller\IndexController::class, 'webhook']);
});
