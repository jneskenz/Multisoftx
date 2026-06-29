<?php

use Illuminate\Support\Facades\Route;
use Modules\ERP\App\Http\Controllers\ERPController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('erp', ERPController::class)->names('erp');
});
