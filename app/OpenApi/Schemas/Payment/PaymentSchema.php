<?php

declare(strict_types=1);

namespace App\OpenApi\Schemas\Payment;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Payment",
    type: "object",
    description: "Pago de una orden",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "order_id", type: "integer", example: 1),
        new OA\Property(property: "payment_method_id", type: "integer", example: 1),
        new OA\Property(property: "amount", type: "number", format: "float", example: 150.50),
        new OA\Property(
            property: "reference",
            type: "string",
            nullable: true,
            example: "REF123456789",
            description: "Número de referencia de la transferencia"
        ),
        new OA\Property(
            property: "receipt_image",
            type: "string",
            nullable: true,
            example: "receipts/payment_123_receipt.jpg",
            description: "Ruta al comprobante de pago"
        ),
        new OA\Property(
            property: "notes",
            type: "string",
            nullable: true,
            example: "Transferencia realizada desde cuenta personal",
            description: "Notas adicionales del cliente"
        ),
        new OA\Property(
            property: "status",
            type: "string",
            enum: ["pending", "verified", "rejected", "refunded"],
            example: "pending"
        ),
        new OA\Property(property: "verified_at", type: "string", format: "date-time", nullable: true, example: "2024-01-15T15:30:00Z"),
        new OA\Property(property: "verified_by", type: "integer", nullable: true, example: 1),
        new OA\Property(property: "store_id", type: "integer", example: 1),
        new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2024-01-15T10:30:00Z"),
        new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2024-01-15T10:30:00Z"),
        new OA\Property(
            property: "order",
            type: "object",
            nullable: true,
            description: "Información básica de la orden",
            properties: [
                new OA\Property(property: "id", type: "integer", example: 1),
                new OA\Property(property: "order_number", type: "string", example: "ORD-20240115120000-1234"),
                new OA\Property(property: "total", type: "number", format: "float", example: 150.50)
            ]
        ),
        new OA\Property(
            property: "payment_method",
            type: "object",
            nullable: true,
            description: "Método de pago utilizado",
            ref: "#/components/schemas/PaymentMethod"
        )
    ]
)]
class PaymentSchema {} 