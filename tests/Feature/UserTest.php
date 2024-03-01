<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserTest extends TestCase
{
    use WithFaker;

    public function test_can_user_register(): void
    {
        $name = $this->faker->userName();
        $email = $this->faker->unique()->safeEmail();
        $password = '123456789';
        $password_confirmation = '123456789';
        $role = 'user';

        $response = $this->postJson('/api/users/register', [
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $password_confirmation,
        ]);

        $retrievedUser = User::where('email', $email)->first();

        $response
            ->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'user created successfully',
                'result' => [
                    'id' => $retrievedUser->id,
                    'name' => $name,
                    'email' => $email,
                    'role' => $role
                ],
            ]);

        $this->assertTrue(Hash::check($password, $retrievedUser->password));

        $this->assertDatabaseHas('users', [
            'name' => $name,
            'email' => $email,
            'role' => $role,
        ]);
    }

    public function test_can_admin_register(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        $name = $this->faker->userName();
        $email = $this->faker->unique()->safeEmail();
        $password = '123456789';
        $password_confirmation = '123456789';
        $role = 'admin';

        $response = $this->actingAs($user, 'api')
            ->postJson('/api/users/admin-register', [
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'password_confirmation' => $password_confirmation,
            ]);

        $retrievedUser = User::where('email', $email)->first();

        $response
            ->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'admin created successfully',
                'result' => [
                    'id' => $retrievedUser->id,
                    'name' => $name,
                    'email' => $email,
                    'role' => $role
                ],
            ]);

        $this->assertTrue(Hash::check($password, $retrievedUser->password));

        $this->assertDatabaseHas('users', [
            'name' => $name,
            'email' => $email,
            'role' => $role,
        ]);
    }

    public function test_validation_error_data_is_required_in_user_register(): void
    {
        $response = $this->postJson('/api/users/register', [
            'name' => '',
            'email' => '',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonPath('exception.error', 'validation error')
            ->assertJsonStructure([
                'success',
                'message',
                'result',
                'exception' => [
                    'error',
                ]
            ]);
    }

    public function test_email_already_exists(): void
    {
        $user = User::factory()->create([
            'role' => 'admin'
        ]);

        $response = $this->postJson('/api/users/register', [
            'name' => $this->faker->unique()->userName(),
            'email' => $user->email,
            'password' => '123456789',
            'password_confirmation' => '123456789',
        ]);
        $response
            ->assertStatus(422)
            ->assertJsonPath('exception.error', 'validation error')
            ->assertJsonStructure([
                'success',
                'message',
                'result',
                'exception' => [
                    'error',
                ]
            ]);
    }

    public function test_can_user_login_using_email_and_password(): void
    {

        $user = User::factory()->create([
            'role' => 'user'
        ]);

        $response = $this->postJson('/api/users/login', [
            'email' => $user->email,
            'password' => '123456789',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'user logged-in successfully',
                'result' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role,
                    ],
                ],
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'result' => [
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'role',
                        'created_at',
                        'updated_at',
                    ],
                    'access_token',
                ],
            ]);
    }

    public function test_can_not_user_login_password_is_wrong(): void
    {

        $user = User::factory()->create([
            'role' => 'user'
        ]);

        $response = $this->postJson('/api/users/login', [
            'email' => $user->email,
            'password' => '12345678912345',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'an error occurred',
                'result' => null,
                'exception' => [
                    'error' => 'Unauthorized'
                ]
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'result',
                'exception' => [
                    'error'
                ]
            ]);
    }

    // public function test_can_user_logout(): void
    // {
    //     $user = User::factory()->create([
    //         'role' => 'user',
    //     ]);

    //     $response = $this->actingAs($user, 'api')
    //         ->getJson('/api/users/logout', []);
    //     $response->assertStatus(200)
    //         ->assertJson([
    //             'success' => true,
    //             'message' => 'user logged-out successfully',
    //             'result' => null,
    //         ])
    //         ->assertJsonStructure([
    //             'success',
    //             'message',
    //             'result',
    //         ]);
    // }
}
