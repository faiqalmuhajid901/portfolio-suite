<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('login screen can be rendered', function (): void {
    $this->get(route('login'))
        ->assertOk()
        ->assertSee('Log in');
});

test('users can authenticate using the login form', function (): void {
    $user = User::factory()->create([
        'password' => Hash::make('password'),
    ]);

    $response = $this->post(route('login.post'), [
        'email' => $user->email,
        'password' => 'password',
        'remember' => false,
    ]);

    $response->assertRedirect(
        route('dashboard', absolute: false)
    );

    $this->assertAuthenticatedAs($user);
});

test('users can not authenticate with invalid password', function (): void {
    $user = User::factory()->create([
        'password' => Hash::make('password'),
    ]);

    $response = $this
        ->from(route('login'))
        ->post(route('login.post'), [
            'email' => $user->email,
            'password' => 'wrong-password',
            'remember' => false,
        ]);

    $response
        ->assertRedirect(route('login'))
        ->assertSessionHasErrors('email');

    $this->assertGuest();
});

test('authenticated dashboard can be rendered', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Portfolio Suite')
        ->assertSee('Analytics')
        ->assertSee('Projects')
        ->assertSee('Settings');
});

test('users can logout', function (): void {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->post(route('logout'));

    $response->assertRedirect('/');

    $this->assertGuest();
});
