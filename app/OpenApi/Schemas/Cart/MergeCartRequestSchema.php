<?php

declare(strict_types=1);

namespace App\OpenApi\Schemas\Cart;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "MergeCartRequest",
    type: "object",
    description: "Datos requeridos para fusionar carrito de invitado con usuario autenticado",
    required: ["guest_session_id"],
    properties: [
        new OA\Property(
            property: "guest_session_id",
            type: "string",
            format: "uuid",
            description: "ID de sesión del carrito de invitado",
            example: "550e8400-e29b-41d4-a716-446655440000"
        )
    ]
)]
class MergeCartRequestSchema {}
