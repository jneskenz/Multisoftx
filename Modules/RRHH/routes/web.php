<?php

use Illuminate\Support\Facades\Route;
use Modules\RRHH\App\Http\Controllers\RRHHController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('rrhh', RRHHController::class)->names('rrhh');
});
