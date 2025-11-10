<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_user()
    {
        $userData = [
            'full_name' => 'Test User',
            'f_name' => 'Test',
            'date_of_birth' => '1990-01-01',
            'email' => 'test@example.com',
            'password' => 'T3st@P4ssw0rd2025!',  // More complex password to pass uncompromised check
            'gender' => 'male',
            'phone_no' => '1234567890',
            'profile_pic' => null,
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'full_name' => $userData['full_name'],
                'email' => $userData['email'],
            ]);
    }
}
