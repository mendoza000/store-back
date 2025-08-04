<?php

declare(strict_types=1);

namespace App\OpenApi\Schemas\Cart;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "UpdateCartItemRequest",
    type: "object",
    description: "Datos requeridos para actualizar un item del carrito",
    required: ["quantity"],
    properties: [
        new OA\Property(
            property: "quantity",
            type: "integer",
            minimum: 1,
            maximum: 999,
            description: "Nueva cantidad del producto",
            example: 3
        )
    ]
)]
class UpdateCartItemRequestSchema {}
