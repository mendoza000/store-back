<?php

declare(strict_types=1);

namespace App\OpenApi\Schemas\Payment;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "PaymentRejectRequest",
    type: "object",
    description: "Datos para rechazar un pago (solo administradores)",
    required: ["reason"],
    properties: [
        new OA\Property(
            property: "reason",
            type: "string",
            maxLength: 255,
            description: "Razón del rechazo",
            example: "Comprobante ilegible"
        ),
        new OA\Property(
            property: "notes",
            type: "string",
            nullable: true,
            maxLength: 1000,
            description: "Notas adicionales del administrador",
            example: "La imagen del comprobante no se puede leer claramente. Por favor, envíe una imagen más nítida."
        ),
        new OA\Property(
            property: "notify_customer",
            type: "boolean",
            nullable: true,
            description: "Notificar al cliente sobre el rechazo",
            example: true
        )
    ]
)]
class PaymentRejectRequestSchema {} 