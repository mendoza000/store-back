<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\UpdateProfileRequest;
use App\Http\Traits\ApiResponseTrait;
use App\Models\User;
use App\Services\CurrentStore;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use OpenApi\Attributes as OA;

/**
 * AuthController - Versión Refactorizada
 * 
 * Este controlador usa la nueva estructura modular de OpenAPI con:
 * - Esquemas separados para requests/responses
 * - Documentación OpenAPI en clases dedicadas
 * - Código limpio y enfocado en la lógica de negocio
 * 
 * @see App\OpenApi\Documentation\AuthEndpoints Para documentación de endpoints
 * @see App\OpenApi\Schemas\Auth\ Para esquemas de requests/responses
 * @see App\OpenApi\Schemas\User\UserSchema Para esquema de usuario
 */
#[OA\Tag(
    name: "Authentication",
    description: "Endpoints para autenticación y gestión de usuarios"
)]
class AuthController extends Controller
{
    use ApiResponseTrait;

    /**
     * Iniciar sesión
     * 
     * @see App\OpenApi\Documentation\AuthEndpoints::login() Para documentación OpenAPI
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->getCredentials();

        if (!$this->attemptLogin($credentials)) {
            return $this->unauthenticatedResponse('Credenciales inválidas.');
        }

        $user = $this->getAuthenticatedUser($credentials['email']);

        if (!$user->isActive()) {
            return $this->forbiddenResponse('Tu cuenta está desactivada. Contacta al administrador.');
        }

        $token = $this->createAuthToken($user, $request->shouldRemember());

        return $this->successResponse([
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => null,
            'user' => $this->formatUserResponse($user),
        ], 'Login exitoso');
    }

    /**
     * Registrar nuevo usuario
     * 
     * @see App\OpenApi\Documentation\AuthEndpoints::register() Para documentación OpenAPI
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        // Requiere tienda (tenant) para asociar al usuario
        if (!CurrentStore::has()) {
            return $this->errorResponse(
                'STORE_REQUIRED',
                "Debe especificar la tienda mediante el header 'X-Store-Id' (o 'Store-Id') para registrar usuarios.",
                400
            );
        }

        $userData = $request->getUserData();
        $userData['store_id'] = CurrentStore::id();

        $user = $this->createUser($userData);
        $token = $this->createAuthToken($user);

        return $this->successResponse([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => $this->formatUserResponse($user),
        ], 'Usuario registrado exitosamente', 201);
    }

    /**
     * Cerrar sesión
     * 
     * @see App\OpenApi\Documentation\AuthEndpoints::logout() Para documentación OpenAPI
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return $this->successResponse([], 'Sesión cerrada exitosamente');
    }

    /**
     * Renovar token
     * 
     * @see App\OpenApi\Documentation\AuthEndpoints::refresh() Para documentación OpenAPI
     */
    public function refresh(Request $request): JsonResponse
    {
        $user = $request->user();
        $request->user()->currentAccessToken()->delete();
        $newToken = $this->createAuthToken($user);

        return $this->successResponse([
            'token' => $newToken,
            'token_type' => 'Bearer',
        ], 'Token renovado exitosamente');
    }

    /**
     * Obtener usuario autenticado
     * 
     * @see App\OpenApi\Documentation\AuthEndpoints::me() Para documentación OpenAPI
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return $this->successResponse(
            $this->formatUserResponse($user),
            'Usuario obtenido exitosamente'
        );
    }

    /**
     * Actualizar perfil del usuario
     */
    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();
        $profileData = $request->getProfileData();
        $user->update($profileData);

        return $this->successResponse(
            $this->formatUserResponse($user->fresh()),
            'Perfil actualizado exitosamente'
        );
    }

    /**
     * Cambiar contraseña
     */
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $user = $request->user();
        $newPassword = $request->getNewPassword();
        $user->update(['password' => Hash::make($newPassword)]);
        $user->tokens()->delete();

        return $this->successResponse([], 'Contraseña cambiada exitosamente');
    }

    /**
     * Solicitar reset de contraseña
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $email = $request->getEmail();
        $status = Password::sendResetLink(['email' => $email]);

        if ($status === Password::RESET_LINK_SENT) {
            return $this->successResponse([], 'Se ha enviado un enlace de recuperación a tu email');
        }

        return $this->errorResponse(
            'PASSWORD_RESET_FAILED',
            'No se pudo enviar el enlace de recuperación. Intenta nuevamente.',
            400
        );
    }

    /**
     * Confirmar reset de contraseña
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $credentials = $request->getResetCredentials();

        $status = Password::reset(
            $credentials,
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                $user->tokens()->delete();
                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return $this->successResponse([], 'Contraseña reseteada exitosamente');
        }

        return $this->errorResponse(
            'PASSWORD_RESET_FAILED',
            'Token inválido o expirado',
            400
        );
    }

    // ============================================================================
    // MÉTODOS PRIVADOS (lógica de negocio sin documentación OpenAPI)
    // ============================================================================

    private function attemptLogin(array $credentials): bool
    {
        return Auth::attempt($credentials);
    }

    private function getAuthenticatedUser(string $email): User
    {
        return User::where('email', $email)->first();
    }

    private function createAuthToken(User $user, bool $remember = false): string
    {
        $tokenName = $remember ? 'remember-token' : 'auth-token';
        return $user->createToken($tokenName)->plainTextToken;
    }

    private function createUser(array $userData): User
    {
        return User::create($userData);
    }

    private function formatUserResponse(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'status' => $user->status,
            'phone' => $user->phone,
            'avatar' => $user->avatar,
            'email_verified_at' => $user->email_verified_at?->toISOString(),
            'created_at' => $user->created_at->toISOString(),
            'updated_at' => $user->updated_at->toISOString(),
        ];
    }
}
