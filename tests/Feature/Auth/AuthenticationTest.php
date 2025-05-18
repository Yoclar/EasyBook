<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create([
        'password' => bcrypt('password'),
        'two_factor_secret' => null,
    ]);

    $response = $this->followingRedirects()->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

   $response->assertSee('Dashboard'); // vagy valami, ami a dashboardon van

    $this->assertAuthenticatedAs($user);
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create([
        'password' => Hash::make('password'),
        'two_factor_secret' => null,
    ]);

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('users can logout', function () {
    $user = User::factory()->create([
        'password' => Hash::make('password'),
        'two_factor_secret' => null,
    ]);

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/');
});
