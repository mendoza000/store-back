<?php

declare(strict_types=1);

namespace App\OpenApi\Schemas\Auth;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "AuthResponse",
    type: "object",
    description: "Respuesta exitosa de autenticación",
    properties: [
        new OA\Property(
            property: "success",
            type: "boolean",
            description: "Indica si la operación fue exitosa",
            example: true
        ),
        new OA\Property(
            property: "message",
            type: "string",
            description: "Mensaje descriptivo",
            example: "Login exitoso"
        ),
        new OA\Property(
            property: "data",
            type: "object",
            properties: [
                new OA\Property(
                    property: "token",
                    type: "string",
                    description: "Token de acceso",
                    example: "1|abc123..."
                ),
                new OA\Property(
                    property: "token_type",
                    type: "string",
                    description: "Tipo de token",
                    example: "Bearer"
                ),
                new OA\Property(
                    property: "expires_in",
                    type: "integer",
                    nullable: true,
                    description: "Tiempo de expiración en segundos (null si no expira)",
                    example: null
                ),
                new OA\Property(
                    property: "user",
                    ref: "#/components/schemas/User",
                    description: "Información del usuario autenticado"
                )
            ]
        )
    ]
)]
class AuthResponseSchema {}
