<?php

namespace Tests\Feature\Auth;

use Illuminate\Support\Facades\Route;

test('public registration route is disabled', function () {
    expect(Route::has('register'))->toBeFalse();

    $this->get('/register')
        ->assertNotFound();
});

test('direct registration submission is rejected', function () {
    $this->post('/register', [
        'name' => 'Unauthorized User',
        'email' => 'unauthorized@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertNotFound();
});
