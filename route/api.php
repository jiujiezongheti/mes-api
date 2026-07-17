<?php

use Webman\Route;

Route::group('/api', function () {
    Route::post('/webhook', [app\api\controller\IndexController::class, 'webhook']);
});
