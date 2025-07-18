<?php

declare(strict_types=1);

namespace App\OpenApi\Schemas\Auth;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "RegisterRequest",
    type: "object",
    description: "Datos requeridos para registrar un nuevo usuario",
    required: ["name", "email", "password", "password_confirmation"],
    properties: [
        new OA\Property(
            property: "name",
            type: "string",
            description: "Nombre completo del usuario",
            example: "Juan Pérez"
        ),
        new OA\Property(
            property: "email",
            type: "string",
            format: "email",
            description: "Email del usuario",
            example: "juan@example.com"
        ),
        new OA\Property(
            property: "password",
            type: "string",
            description: "Contraseña del usuario",
            example: "Password123!"
        ),
        new OA\Property(
            property: "password_confirmation",
            type: "string",
            description: "Confirmación de la contraseña",
            example: "Password123!"
        ),
        new OA\Property(
            property: "phone",
            type: "string",
            nullable: true,
            description: "Número de teléfono",
            example: "+58 412 1234567"
        ),
        new OA\Property(
            property: "role",
            type: "string",
            enum: ["admin", "customer", "moderator"],
            description: "Rol del usuario",
            example: "customer"
        )
    ]
)]
class RegisterRequestSchema {}
