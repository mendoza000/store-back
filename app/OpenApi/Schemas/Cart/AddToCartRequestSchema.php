<?php

declare(strict_types=1);

namespace App\OpenApi\Schemas\Cart;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "AddToCartRequest",
    type: "object",
    description: "Datos requeridos para agregar un producto al carrito",
    required: ["product_id", "quantity"],
    properties: [
        new OA\Property(
            property: "product_id",
            type: "integer",
            minimum: 1,
            description: "ID del producto a agregar",
            example: 1
        ),
        new OA\Property(
            property: "quantity",
            type: "integer",
            minimum: 1,
            maximum: 999,
            description: "Cantidad del producto",
            example: 2
        ),
        new OA\Property(
            property: "price",
            type: "number",
            format: "float",
            minimum: 0,
            maximum: 999999.99,
            nullable: true,
            description: "Precio unitario del producto (opcional, se obtiene automáticamente si no se especifica)",
            example: 99.99
        )
    ]
)]
class AddToCartRequestSchema {}
