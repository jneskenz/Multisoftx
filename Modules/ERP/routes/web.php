<?php

use Illuminate\Support\Facades\Route;
use Modules\ERP\App\Http\Controllers\ERPController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('erp', ERPController::class)->names('erp');
});
