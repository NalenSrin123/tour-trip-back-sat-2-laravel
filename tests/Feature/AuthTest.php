<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_register_customer(): void
    {
        $payload = [
            'full_name'             => 'Jane Doe',
            'email'                 => 'jane@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'phone'                 => '1234567890',
        ];

        $response = $this->postJson('/api/register', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Registered successfully',
            ]);

        $this->assertDatabaseHas('users', [
            'email'     => 'jane@example.com',
            'full_name' => 'Jane Doe',
            'role'      => 'customer',
        ]);
    }

    public function test_can_login_customer(): void
    {
        $this->postJson('/api/register', [
            'full_name'             => 'John Doe',
            'email'                 => 'john@example.com',
            'password'              => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response = $this->postJson('/api/login', [
            'email'    => 'john@example.com',
            'password' => 'secret123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'Message',
                'User',
                'token',
            ]);
    }
}
