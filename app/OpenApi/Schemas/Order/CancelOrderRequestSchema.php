<?php

declare(strict_types=1);

namespace App\OpenApi\Schemas\Order;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "CancelOrderRequest",
    type: "object",
    description: "Datos para cancelar un pedido",
    properties: [
        new OA\Property(
            property: "reason",
            type: "string",
            enum: [
                "customer_request",
                "payment_issues",
                "address_issues",
                "product_unavailable",
                "duplicate_order",
                "other"
            ],
            nullable: true,
            description: "Razón de la cancelación",
            example: "customer_request"
        ),
        new OA\Property(
            property: "notes",
            type: "string",
            maxLength: 1000,
            nullable: true,
            description: "Notas adicionales sobre la cancelación",
            example: "El cliente cambió de opinión sobre la compra"
        )
    ]
)]
class CancelOrderRequestSchema {}
