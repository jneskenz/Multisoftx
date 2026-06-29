<?php

use Illuminate\Support\Facades\Route;
use Modules\RRHH\App\Http\Controllers\RRHHController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('rrhh', RRHHController::class)->names('rrhh');
});
