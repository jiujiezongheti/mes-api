<?php

use Webman\Route;

Route::group('/mobile', function () {
    Route::post('/auth/login', [app\mobile\controller\AuthController::class, 'login']);
    Route::post('/auth/refresh', [app\mobile\controller\AuthController::class, 'refresh']);
    Route::group('/', function () {
        Route::get('/auth/me', [app\mobile\controller\AuthController::class, 'me']);
        Route::get('/dashboard', [app\mobile\controller\IndexController::class, 'dashboard']);
    })->middleware([app\middleware\MobileAuthMiddleware::class]);
});
