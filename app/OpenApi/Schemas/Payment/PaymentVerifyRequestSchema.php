<?php

declare(strict_types=1);

namespace App\OpenApi\Schemas\Payment;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "PaymentVerifyRequest",
    type: "object",
    description: "Datos para verificar/aprobar un pago (solo administradores)",
    properties: [
        new OA\Property(
            property: "notes",
            type: "string",
            nullable: true,
            maxLength: 1000,
            description: "Notas del administrador sobre la verificación",
            example: "Comprobante verificado correctamente"
        ),
        new OA\Property(
            property: "notify_customer",
            type: "boolean",
            nullable: true,
            description: "Notificar al cliente sobre la verificación",
            example: true
        )
    ]
)]
class PaymentVerifyRequestSchema {} 