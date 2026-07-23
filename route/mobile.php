<?php

use Webman\Route;

Route::group('/mobile', function () {
    Route::post('/auth/login', [app\mobile\controller\AuthController::class, 'login']);
    Route::post('/auth/refresh', [app\mobile\controller\AuthController::class, 'refresh']);
    Route::group('/', function () {
        Route::get('/auth/me', [app\mobile\controller\AuthController::class, 'me']);
        Route::get('/dashboard', [app\mobile\controller\IndexController::class, 'dashboard']);
        Route::get('/stock/check-list', [app\mobile\controller\StockController::class, 'checkList']);
        Route::get('/stock/check-items', [app\mobile\controller\StockController::class, 'checkItems']);
        Route::post('/stock/check-complete', [app\mobile\controller\StockController::class, 'checkComplete']);
    })->middleware([app\middleware\MobileAuthMiddleware::class]);

    Route::group('', function () {
        Route::get('/stock/check-task/list', [app\mobile\controller\StockCheckController::class, 'taskList']);
        Route::get('/stock/check-task/detail', [app\mobile\controller\StockCheckController::class, 'taskDetail']);
        Route::post('/stock/check-record/create', [app\mobile\controller\StockCheckController::class, 'recordCreate']);
        Route::post('/stock/check-task/complete', [app\mobile\controller\StockCheckController::class, 'taskComplete']);
        Route::get('/stock/material/by-code', [app\mobile\controller\StockCheckController::class, 'materialByCode']);
        Route::get('/config/batch-control', [app\mobile\controller\StockCheckController::class, 'batchControl']);
    })->middleware([app\middleware\MobileAuthMiddleware::class]);
});
