<?php

declare(strict_types=1);

namespace App\OpenApi\Schemas\Payment;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "PaymentMethodUpdateRequest",
    type: "object",
    description: "Datos para actualizar un método de pago existente",
    properties: [
        new OA\Property(
            property: "name",
            type: "string",
            description: "Nombre descriptivo del método de pago",
            nullable: true,
            example: "Transferencia Bancaria BOD - Actualizado",
            maxLength: 255
        ),
        new OA\Property(
            property: "type",
            type: "string",
            description: "Tipo de método de pago",
            nullable: true,
            enum: ["mobile_payment", "bank_transfer", "paypal", "cash", "crypto"],
            example: "bank_transfer"
        ),
        new OA\Property(
            property: "account_info",
            type: "string",
            description: "Información de la cuenta (JSON string con datos específicos del tipo)",
            nullable: true,
            example: "{\"bank_name\":\"Banco Occidental de Descuento\",\"account_number\":\"0116-0000-00-0000000000\",\"account_holder\":\"Mi Tienda C.A.\",\"document_type\":\"J\",\"document_number\":\"12345678-9\"}"
        ),
        new OA\Property(
            property: "instructions",
            type: "string",
            description: "Instrucciones adicionales para el cliente",
            nullable: true,
            example: "Realizar transferencia y enviar comprobante por este medio. Incluir número de referencia y nombre completo.",
            maxLength: 1000
        ),
        new OA\Property(
            property: "status",
            type: "string",
            description: "Estado del método de pago",
            nullable: true,
            enum: ["active", "inactive"],
            example: "active"
        )
    ],
    example: [
        "name" => "Transferencia Bancaria BOD - Actualizado",
        "account_info" => "{\"bank_name\":\"Banco Occidental de Descuento\",\"account_number\":\"0116-0000-00-0000000000\",\"account_holder\":\"Mi Tienda Actualizada C.A.\",\"document_type\":\"J\",\"document_number\":\"12345678-9\"}",
        "instructions" => "Realizar transferencia y enviar comprobante por este medio. Incluir número de referencia y nombre completo.",
        "status" => "active"
    ]
)]
class PaymentMethodUpdateRequestSchema {}
