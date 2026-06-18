<?php

use App\Models\User;

test('guests are redirected to the login page', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $response = $this->get(route('dashboard', $team));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $response = $this
        ->actingAs($user)
        ->get(route('dashboard', $team));

    $response
        ->assertOk()
        ->assertSee('layout-content-navbar', false)
        ->assertSee('bg-menu-theme', false)
        ->assertSee('vuexy/vendor/css/core.css', false)
        ->assertDontSee('theme-default.css', false);
});
