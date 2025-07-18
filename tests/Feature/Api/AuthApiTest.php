<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    protected string $apiPrefix = '/api/v1/auth';

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'status' => User::STATUS_ACTIVE,
        ]);

        $response = $this->postJson("{$this->apiPrefix}/login", [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'token',
                    'token_type',
                    'expires_in',
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'role',
                        'status',
                        'created_at',
                    ],
                ],
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Login exitoso',
                'data' => [
                    'token_type' => 'Bearer',
                    'user' => [
                        'email' => 'test@example.com',
                        'status' => User::STATUS_ACTIVE,
                    ],
                ],
            ]);

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
        ]);
    }

    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson("{$this->apiPrefix}/login", [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'error' => [
                    'code' => 'UNAUTHENTICATED',
                    'message' => 'Credenciales inválidas.',
                ],
            ]);
    }

    public function test_inactive_user_cannot_login(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'status' => User::STATUS_INACTIVE,
        ]);

        $response = $this->postJson("{$this->apiPrefix}/login", [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'error' => [
                    'code' => 'FORBIDDEN',
                    'message' => 'Tu cuenta está desactivada. Contacta al administrador.',
                ],
            ]);
    }

    public function test_user_can_register_with_valid_data(): void
    {
        $userData = [
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'phone' => '+58 412 1234567',
        ];

        $response = $this->postJson("{$this->apiPrefix}/register", $userData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'token',
                    'token_type',
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'role',
                        'phone',
                    ],
                ],
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Usuario registrado exitosamente',
                'data' => [
                    'user' => [
                        'name' => 'Juan Pérez',
                        'email' => 'juan@example.com',
                        'role' => User::ROLE_CUSTOMER,
                        'phone' => '+58 412 1234567',
                    ],
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'role' => User::ROLE_CUSTOMER,
            'status' => User::STATUS_ACTIVE,
        ]);
    }

    public function test_user_cannot_register_with_existing_email(): void
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->postJson("{$this->apiPrefix}/register", [
            'name' => 'Test User',
            'email' => 'existing@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson("{$this->apiPrefix}/logout");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Sesión cerrada exitosamente',
            ]);
    }

    public function test_authenticated_user_can_refresh_token(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson("{$this->apiPrefix}/refresh");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'token',
                    'token_type',
                ],
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Token renovado exitosamente',
                'data' => [
                    'token_type' => 'Bearer',
                ],
            ]);
    }

    public function test_authenticated_user_can_get_profile(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        Sanctum::actingAs($user);

        $response = $this->getJson("{$this->apiPrefix}/me");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'name',
                    'email',
                    'role',
                    'status',
                    'created_at',
                ],
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => 'Test User',
                    'email' => 'test@example.com',
                ],
            ]);
    }

    public function test_authenticated_user_can_update_profile(): void
    {
        $user = User::factory()->create([
            'name' => 'Old Name',
            'email' => 'old@example.com',
        ]);
        Sanctum::actingAs($user);

        $updateData = [
            'name' => 'New Name',
            'phone' => '+58 424 9876543',
        ];

        $response = $this->putJson("{$this->apiPrefix}/profile", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Perfil actualizado exitosamente',
                'data' => [
                    'name' => 'New Name',
                    'phone' => '+58 424 9876543',
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'New Name',
            'phone' => '+58 424 9876543',
        ]);
    }

    public function test_authenticated_user_can_change_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('oldPassword123'),
        ]);
        Sanctum::actingAs($user);

        $response = $this->postJson("{$this->apiPrefix}/change-password", [
            'current_password' => 'oldPassword123',
            'password' => 'NewPassword456!',
            'password_confirmation' => 'NewPassword456!',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Contraseña cambiada exitosamente',
            ]);

        // Verify password was changed
        $user->refresh();
        $this->assertTrue(Hash::check('NewPassword456!', $user->password));

        // Verify all tokens were revoked
        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $user->id,
        ]);
    }

    public function test_user_cannot_change_password_with_wrong_current_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('correctPassword123'),
        ]);
        Sanctum::actingAs($user);

        $response = $this->postJson("{$this->apiPrefix}/change-password", [
            'current_password' => 'wrongPassword',
            'password' => 'NewPassword456!',
            'password_confirmation' => 'NewPassword456!',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['current_password']);
    }

    public function test_user_can_request_password_reset(): void
    {
        $user = User::factory()->create(['email' => 'test@example.com']);

        $response = $this->postJson("{$this->apiPrefix}/forgot-password", [
            'email' => 'test@example.com',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Se ha enviado un enlace de recuperación a tu email',
            ]);
    }

    public function test_user_cannot_request_password_reset_for_nonexistent_email(): void
    {
        $response = $this->postJson("{$this->apiPrefix}/forgot-password", [
            'email' => 'nonexistent@example.com',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_user_can_reset_password_with_valid_token(): void
    {
        $user = User::factory()->create(['email' => 'test@example.com']);
        $token = Password::createToken($user);

        $response = $this->postJson("{$this->apiPrefix}/reset-password", [
            'token' => $token,
            'email' => 'test@example.com',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Contraseña reseteada exitosamente',
            ]);

        // Verify password was changed
        $user->refresh();
        $this->assertTrue(Hash::check('NewPassword123!', $user->password));
    }

    public function test_login_validation_errors(): void
    {
        $response = $this->postJson("{$this->apiPrefix}/login", []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_register_validation_errors(): void
    {
        $response = $this->postJson("{$this->apiPrefix}/register", [
            'name' => '',
            'email' => 'invalid-email',
            'password' => '123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'name',
                'email',
                'password',
                'password_confirmation',
            ]);
    }

    public function test_unauthenticated_user_cannot_access_protected_routes(): void
    {
        $protectedRoutes = [
            'GET' => ['/me'],
            'POST' => ['/logout', '/refresh', '/change-password'],
            'PUT' => ['/profile'],
        ];

        foreach ($protectedRoutes as $method => $routes) {
            foreach ($routes as $route) {
                $response = $this->json($method, $this->apiPrefix . $route);
                $response->assertStatus(401);
            }
        }
    }
}
