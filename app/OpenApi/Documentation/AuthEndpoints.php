<?php

declare(strict_types=1);

namespace App\OpenApi\Documentation;

use OpenApi\Attributes as OA;

/**
 * Documentación de endpoints de autenticación
 */
class AuthEndpoints
{
    #[OA\Post(
        path: "/api/v1/auth/login",
        summary: "Iniciar sesión",
        description: "Autentica un usuario y devuelve un token de acceso",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/LoginRequest")
        ),
        tags: ["Authentication"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Login exitoso",
                content: new OA\JsonContent(ref: "#/components/schemas/AuthResponse")
            ),
            new OA\Response(
                response: 401,
                description: "Credenciales inválidas",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            ),
            new OA\Response(
                response: 422,
                description: "Error de validación",
                content: new OA\JsonContent(ref: "#/components/schemas/ValidationErrorResponse")
            )
        ]
    )]
    public function login() {}

    #[OA\Post(
        path: "/api/v1/auth/register",
        summary: "Registrar nuevo usuario",
        description: "Registra un nuevo usuario en el sistema",
        parameters: [
            new OA\Parameter(ref: "#/components/parameters/X-Store-Id"),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/RegisterRequest")
        ),
        tags: ["Authentication"],
        responses: [
            new OA\Response(
                response: 201,
                description: "Usuario registrado exitosamente",
                content: new OA\JsonContent(ref: "#/components/schemas/AuthResponse")
            ),
            new OA\Response(
                response: 422,
                description: "Error de validación",
                content: new OA\JsonContent(ref: "#/components/schemas/ValidationErrorResponse")
            )
        ]
    )]
    public function register() {}

    #[OA\Post(
        path: "/api/v1/auth/logout",
        summary: "Cerrar sesión",
        description: "Revoca el token de acceso actual del usuario",
        security: [["sanctum" => []]],
        tags: ["Authentication"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Logout exitoso",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Sesión cerrada exitosamente")
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "No autenticado",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            )
        ]
    )]
    public function logout() {}

    #[OA\Post(
        path: "/api/v1/auth/refresh",
        summary: "Renovar token",
        description: "Revoca el token actual y genera uno nuevo",
        security: [["sanctum" => []]],
        tags: ["Authentication"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Token renovado exitosamente",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Token renovado exitosamente"),
                        new OA\Property(
                            property: "data",
                            properties: [
                                new OA\Property(property: "token", type: "string", example: "3|ghi789..."),
                                new OA\Property(property: "token_type", type: "string", example: "Bearer")
                            ],
                            type: "object"
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "No autenticado",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            )
        ]
    )]
    public function refresh() {}

    #[OA\Get(
        path: "/api/v1/auth/me",
        summary: "Obtener usuario autenticado",
        description: "Devuelve la información del usuario autenticado",
        security: [["sanctum" => []]],
        tags: ["Authentication"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Información del usuario",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Usuario obtenido exitosamente"),
                        new OA\Property(property: "data", ref: "#/components/schemas/User")
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "No autenticado",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            )
        ]
    )]
    public function me() {}
}
