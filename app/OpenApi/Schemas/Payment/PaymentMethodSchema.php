<?php

declare(strict_types=1);

namespace App\OpenApi\Schemas\Payment;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "PaymentMethod",
    type: "object",
    description: "Método de pago disponible",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "name", type: "string", example: "Transferencia Bancaria"),
        new OA\Property(
            property: "type", 
            type: "string", 
            enum: ["bank_transfer", "mobile_payment", "cash", "crypto"],
            example: "bank_transfer"
        ),
        new OA\Property(
            property: "account_info",
            type: "object",
            description: "Información de la cuenta del comercio",
            properties: [
                new OA\Property(property: "bank_name", type: "string", example: "Banco de Venezuela"),
                new OA\Property(property: "account_number", type: "string", example: "0102-0000-00-0000000000"),
                new OA\Property(property: "account_holder", type: "string", example: "Mi Tienda C.A."),
                new OA\Property(property: "document_type", type: "string", example: "J"),
                new OA\Property(property: "document_number", type: "string", example: "12345678-9")
            ]
        ),
        new OA\Property(
            property: "instructions",
            type: "string",
            nullable: true,
            example: "Realizar transferencia y enviar comprobante por este medio"
        ),
        new OA\Property(
            property: "status",
            type: "string",
            enum: ["active", "inactive"],
            example: "active"
        ),
        new OA\Property(property: "store_id", type: "integer", example: 1),
        new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2024-01-15T10:30:00Z"),
        new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2024-01-15T10:30:00Z")
    ]
)]
class PaymentMethodSchema {} 