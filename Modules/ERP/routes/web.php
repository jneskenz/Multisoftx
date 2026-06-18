<?php

use Illuminate\Support\Facades\Route;
use Modules\ERP\App\Http\Controllers\ERPController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('erps', ERPController::class)->names('erp');
});
