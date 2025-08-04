<?php

declare(strict_types=1);

namespace App\OpenApi\Schemas\Order;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "CreateOrderRequest",
    type: "object",
    description: "Datos requeridos para crear un pedido desde el carrito",
    required: ["shipping_address"],
    properties: [
        new OA\Property(
            property: "shipping_address",
            type: "object",
            description: "Dirección de envío",
            required: ["first_name", "last_name", "address_line_1", "city", "state", "postal_code", "country", "phone"],
            properties: [
                new OA\Property(property: "first_name", type: "string", maxLength: 50, example: "Juan"),
                new OA\Property(property: "last_name", type: "string", maxLength: 50, example: "Pérez"),
                new OA\Property(property: "company", type: "string", maxLength: 100, nullable: true, example: "Mi Empresa S.A."),
                new OA\Property(property: "address_line_1", type: "string", maxLength: 255, example: "Av. Principal 123"),
                new OA\Property(property: "address_line_2", type: "string", maxLength: 255, nullable: true, example: "Apto 4B"),
                new OA\Property(property: "city", type: "string", maxLength: 100, example: "Caracas"),
                new OA\Property(property: "state", type: "string", maxLength: 100, example: "Distrito Capital"),
                new OA\Property(property: "postal_code", type: "string", maxLength: 20, example: "1050"),
                new OA\Property(property: "country", type: "string", maxLength: 100, example: "Venezuela"),
                new OA\Property(property: "phone", type: "string", maxLength: 20, example: "+58-212-555-0123")
            ]
        ),
        new OA\Property(
            property: "billing_address",
            type: "object",
            nullable: true,
            description: "Dirección de facturación (opcional, usa shipping_address si no se proporciona)",
            properties: [
                new OA\Property(property: "first_name", type: "string", maxLength: 50, example: "Juan"),
                new OA\Property(property: "last_name", type: "string", maxLength: 50, example: "Pérez"),
                new OA\Property(property: "company", type: "string", maxLength: 100, nullable: true, example: "Mi Empresa S.A."),
                new OA\Property(property: "address_line_1", type: "string", maxLength: 255, example: "Av. Principal 123"),
                new OA\Property(property: "address_line_2", type: "string", maxLength: 255, nullable: true, example: "Apto 4B"),
                new OA\Property(property: "city", type: "string", maxLength: 100, example: "Caracas"),
                new OA\Property(property: "state", type: "string", maxLength: 100, example: "Distrito Capital"),
                new OA\Property(property: "postal_code", type: "string", maxLength: 20, example: "1050"),
                new OA\Property(property: "country", type: "string", maxLength: 100, example: "Venezuela"),
                new OA\Property(property: "phone", type: "string", maxLength: 20, example: "+58-212-555-0123")
            ]
        ),
        new OA\Property(
            property: "notes",
            type: "string",
            maxLength: 1000,
            nullable: true,
            description: "Notas adicionales para el pedido",
            example: "Entregar en horario de oficina"
        ),
        new OA\Property(
            property: "use_shipping_as_billing",
            type: "boolean",
            nullable: true,
            description: "Usar la dirección de envío como dirección de facturación",
            example: true
        )
    ]
)]
class CreateOrderRequestSchema {}
