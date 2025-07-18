<?php

declare(strict_types=1);

namespace App\OpenApi\Schemas\Auth;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "LoginRequest",
    type: "object",
    description: "Datos requeridos para iniciar sesión",
    required: ["email", "password"],
    properties: [
        new OA\Property(
            property: "email",
            type: "string",
            format: "email",
            description: "Email del usuario",
            example: "admin@example.com"
        ),
        new OA\Property(
            property: "password",
            type: "string",
            description: "Contraseña del usuario",
            example: "password123"
        ),
        new OA\Property(
            property: "remember",
            type: "boolean",
            description: "Recordar sesión",
            example: false
        )
    ]
)]
class LoginRequestSchema {}
