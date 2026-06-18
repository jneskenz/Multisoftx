<?php

use Illuminate\Support\Facades\Route;
use Modules\RRHH\App\Http\Controllers\RRHHController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('rrhhs', RRHHController::class)->names('rrhh');
});
