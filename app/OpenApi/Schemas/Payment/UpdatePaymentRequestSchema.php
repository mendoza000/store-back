<?php

declare(strict_types=1);

namespace App\OpenApi\Schemas\Payment;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "UpdatePaymentRequest",
    type: "object",
    description: "Datos para actualizar un comprobante de pago",
    properties: [
        new OA\Property(
            property: "payment_method_id",
            type: "integer",
            nullable: true,
            description: "ID del método de pago (opcional)",
            example: 1
        ),
        new OA\Property(
            property: "amount",
            type: "number",
            format: "float",
            nullable: true,
            description: "Monto pagado (opcional)",
            example: 150.50
        ),
        new OA\Property(
            property: "reference",
            type: "string",
            nullable: true,
            maxLength: 100,
            description: "Número de referencia de la transferencia",
            example: "REF123456789"
        ),
        new OA\Property(
            property: "receipt_image",
            type: "string",
            format: "binary",
            nullable: true,
            description: "Nuevo comprobante de pago (imagen o PDF)"
        ),
        new OA\Property(
            property: "notes",
            type: "string",
            nullable: true,
            maxLength: 1000,
            description: "Notas adicionales sobre el pago",
            example: "Transferencia corregida con referencia actualizada"
        )
    ]
)]
class UpdatePaymentRequestSchema {} 