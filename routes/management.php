<?php

use App\Http\Middleware\EnsureTeamMembership;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('teams', 'pages::teams.index')->name('teams.index');

    Route::middleware(EnsureTeamMembership::class)->group(function () {
        Route::livewire('teams/{team}', 'pages::teams.edit')->name('teams.edit');
    });

    Route::livewire('users', 'pages::users.index')->name('users.index');
    Route::livewire('users/{user}', 'pages::users.edit')->name('users.edit');

    Route::livewire('roles', 'pages::roles.index')->name('roles.index');
    Route::livewire('roles/{role}', 'pages::roles.edit')->name('roles.edit');
});
