<?php

declare(strict_types=1);

namespace App\OpenApi\Schemas\User;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "User",
    type: "object",
    description: "Modelo de usuario del sistema",
    properties: [
        new OA\Property(
            property: "id",
            type: "integer",
            description: "ID único del usuario",
            example: 1
        ),
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
            property: "role",
            type: "string",
            enum: ["admin", "customer", "moderator"],
            description: "Rol del usuario",
            example: "customer"
        ),
        new OA\Property(
            property: "status",
            type: "string",
            enum: ["active", "inactive", "suspended"],
            description: "Estado del usuario",
            example: "active"
        ),
        new OA\Property(
            property: "phone",
            type: "string",
            nullable: true,
            description: "Número de teléfono",
            example: "+58 412 1234567"
        ),
        new OA\Property(
            property: "avatar",
            type: "string",
            nullable: true,
            description: "URL del avatar del usuario",
            example: "https://example.com/avatar.jpg"
        ),
        new OA\Property(
            property: "email_verified_at",
            type: "string",
            format: "date-time",
            nullable: true,
            description: "Fecha de verificación del email",
            example: "2024-01-15T10:30:00Z"
        ),
        new OA\Property(
            property: "created_at",
            type: "string",
            format: "date-time",
            description: "Fecha de creación",
            example: "2024-01-01T12:00:00Z"
        ),
        new OA\Property(
            property: "updated_at",
            type: "string",
            format: "date-time",
            description: "Fecha de última actualización",
            example: "2024-01-15T10:30:00Z"
        )
    ]
)]
class UserSchema
{
    // Esta clase solo existe para contener la definición del esquema
    // No necesita métodos ni propiedades
}
