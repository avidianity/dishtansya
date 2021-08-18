<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test register with successful response.
     *
     * @return void
     */
    public function test_register_with_successful_response()
    {
        $response = $this->post('/api/register', [
            'email' => $this->faker->email,
            'password' => $this->faker->password,
        ]);

        $response->assertStatus(201)
            ->assertExactJson(['message' => 'User successfully registered']);
    }

    /**
     * Test register failing if email already exists.
     *
     * @return void
     */
    public function test_register_failing_if_email_exists()
    {
        $email = $this->faker->email;
        $password = $this->faker->password;

        // Make user already existing
        User::create([
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        $response = $this->post(
            '/api/register',
            [
                'email' => $email,
                'password' => $password,
            ],
            ['Accept' => 'application/json']
        );

        $response->assertStatus(400)
            ->assertExactJson(['message' => 'Email is already taken']);
    }

    /**
     * Test login with successful response
     *
     * @return void
     */
    public function test_login_with_successful_response()
    {
        $email = $this->faker->email;
        $password = $this->faker->password;

        // Make user already existing
        User::create([
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        $response = $this->post(
            '/api/login',
            [
                'email' => $email,
                'password' => $password,
            ],
            ['Accept' => 'application/json']
        );

        $response->assertStatus(201)
            ->assertJsonStructure(['access_token']);
    }

    /**
     * Test login failing if invalid credentials
     *
     * @return void
     */
    public function test_login_failing_if_invalid_credentials()
    {
        $email = $this->faker->email;
        $password = $this->faker->password;

        // Make user already existing
        User::create([
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        $response = $this->post(
            '/api/login',
            [
                'email' => $email,
                'password' => 'wrong password',
            ],
            ['Accept' => 'application/json']
        );

        $response->assertStatus(401)
            ->assertExactJson(['message' => 'Invalid credentials']);
    }
}
