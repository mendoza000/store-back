<?php

declare(strict_types=1);

namespace App\OpenApi\Schemas\Order;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "UpdateOrderStatusRequest",
    type: "object",
    description: "Datos para actualizar el estado de un pedido (solo administradores)",
    required: ["status"],
    properties: [
        new OA\Property(
            property: "status",
            type: "string",
            enum: ["pending", "paid", "processing", "shipped", "delivered", "cancelled"],
            description: "Nuevo estado del pedido",
            example: "shipped"
        ),
        new OA\Property(
            property: "notes",
            type: "string",
            maxLength: 1000,
            nullable: true,
            description: "Notas sobre el cambio de estado",
            example: "Pedido enviado con empresa de transporte"
        ),
        new OA\Property(
            property: "reason",
            type: "string",
            maxLength: 255,
            nullable: true,
            description: "Razón del cambio de estado",
            example: "order_shipped"
        ),
        new OA\Property(
            property: "notify_customer",
            type: "boolean",
            nullable: true,
            description: "Notificar al cliente sobre el cambio",
            example: true
        ),
        new OA\Property(
            property: "tracking_number",
            type: "string",
            maxLength: 100,
            nullable: true,
            description: "Número de seguimiento (requerido cuando status = shipped)",
            example: "TK123456789VE"
        ),
        new OA\Property(
            property: "carrier",
            type: "string",
            maxLength: 50,
            nullable: true,
            description: "Empresa transportista (requerido cuando status = shipped)",
            example: "MRW"
        )
    ]
)]
class UpdateOrderStatusRequestSchema {}
