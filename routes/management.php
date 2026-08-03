<?php

use App\Http\Middleware\EnsureTeamMembership;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('teams', 'pages::teams.index')->name('teams.index');

    Route::middleware(EnsureTeamMembership::class)->group(function () {
        Route::livewire('teams/{team}', 'pages::teams.edit')->name('teams.edit');
    });

    Route::middleware('can:roles.list')->group(function () {
        Route::livewire('roles', 'pages::roles.index')->name('roles.index');
    });

    Route::middleware('can:roles.edit')->group(function () {
        Route::livewire('roles/{role}', 'pages::roles.edit')->name('roles.edit');
    });
});