<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

/**
 * Test user authentication functionalities including registration, login, and fetching authenticated user details.
 */
it('registers a user', function () {
    $response = $this->postJson('/api/register', [
        'name'     => 'Test User',
        'email'    => 'test@example.com',
        'password' => 'secret123',
        'role'     => 'user',
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure(['data' => ['id', 'email'], 'token']);
});

it('logs in a user', function () {
    $user = User::factory()->create([
        'email' => 'login@example.com',
        'password' => Hash::make('password123'),
    ]);

    $response = $this->postJson('/api/login', [
        'email' => 'login@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure(['data' => ['id', 'email'], 'token']);
});

it('returns the authenticated user', function () {
    $user = User::factory()->create();

    $token = $user->createToken('api')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->getJson('/api/me');

    $response->assertStatus(200)
        ->assertJsonPath('data.id', $user->id);
});
