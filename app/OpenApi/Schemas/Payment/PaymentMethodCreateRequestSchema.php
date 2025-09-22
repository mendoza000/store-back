<?php

declare(strict_types=1);

namespace App\OpenApi\Schemas\Payment;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "PaymentMethodCreateRequest",
    type: "object",
    description: "Datos requeridos para crear un método de pago",
    required: ["name", "type", "account_info"],
    properties: [
        new OA\Property(
            property: "name",
            type: "string",
            description: "Nombre descriptivo del método de pago",
            example: "Transferencia Bancaria BOD",
            maxLength: 255
        ),
        new OA\Property(
            property: "type",
            type: "string",
            description: "Tipo de método de pago",
            enum: ["mobile_payment", "bank_transfer", "paypal", "cash", "crypto"],
            example: "bank_transfer"
        ),
        new OA\Property(
            property: "account_info",
            type: "string",
            description: "Información de la cuenta (JSON string con datos específicos del tipo)",
            example: "{\"bank_name\":\"Banco Occidental de Descuento\",\"account_number\":\"0116-0000-00-0000000000\",\"account_holder\":\"Mi Tienda C.A.\",\"document_type\":\"J\",\"document_number\":\"12345678-9\"}"
        ),
        new OA\Property(
            property: "instructions",
            type: "string",
            description: "Instrucciones adicionales para el cliente",
            nullable: true,
            example: "Realizar transferencia y enviar comprobante por este medio. Incluir número de referencia.",
            maxLength: 1000
        ),
        new OA\Property(
            property: "status",
            type: "string",
            description: "Estado del método de pago",
            enum: ["active", "inactive"],
            default: "active",
            example: "active"
        )
    ],
    example: [
        "name" => "Transferencia Bancaria BOD",
        "type" => "bank_transfer",
        "account_info" => "{\"bank_name\":\"Banco Occidental de Descuento\",\"account_number\":\"0116-0000-00-0000000000\",\"account_holder\":\"Mi Tienda C.A.\",\"document_type\":\"J\",\"document_number\":\"12345678-9\"}",
        "instructions" => "Realizar transferencia y enviar comprobante por este medio. Incluir número de referencia.",
        "status" => "active"
    ]
)]
class PaymentMethodCreateRequestSchema {}