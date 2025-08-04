<?php

declare(strict_types=1);

namespace App\OpenApi\Schemas\Order;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "OrderResponse",
    type: "object",
    description: "Respuesta exitosa de pedido",
    properties: [
        new OA\Property(
            property: "success",
            type: "boolean",
            description: "Indica si la operación fue exitosa",
            example: true
        ),
        new OA\Property(
            property: "message",
            type: "string",
            description: "Mensaje descriptivo",
            example: "Pedido obtenido exitosamente"
        ),
        new OA\Property(
            property: "data",
            type: "object",
            properties: [
                new OA\Property(property: "id", type: "integer", example: 1),
                new OA\Property(property: "order_number", type: "string", example: "ORD-20240115120000-1234"),
                new OA\Property(property: "status", type: "string", example: "pending"),
                new OA\Property(property: "status_label", type: "string", example: "Pendiente"),
                new OA\Property(property: "total", type: "number", format: "float", example: 259.97),
                new OA\Property(property: "subtotal", type: "number", format: "float", example: 199.98),
                new OA\Property(property: "tax_amount", type: "number", format: "float", example: 32.00),
                new OA\Property(property: "shipping_amount", type: "number", format: "float", example: 15.00),
                new OA\Property(property: "discount_amount", type: "number", format: "float", example: 0.00),
                new OA\Property(
                    property: "shipping_address",
                    type: "object",
                    properties: [
                        new OA\Property(property: "first_name", type: "string", example: "Juan"),
                        new OA\Property(property: "last_name", type: "string", example: "Pérez"),
                        new OA\Property(property: "company", type: "string", nullable: true, example: null),
                        new OA\Property(property: "address_line_1", type: "string", example: "Av. Principal 123"),
                        new OA\Property(property: "address_line_2", type: "string", nullable: true, example: "Apto 4B"),
                        new OA\Property(property: "city", type: "string", example: "Caracas"),
                        new OA\Property(property: "state", type: "string", example: "Distrito Capital"),
                        new OA\Property(property: "postal_code", type: "string", example: "1050"),
                        new OA\Property(property: "country", type: "string", example: "Venezuela"),
                        new OA\Property(property: "phone", type: "string", example: "+58-212-555-0123")
                    ]
                ),
                new OA\Property(
                    property: "billing_address",
                    type: "object",
                    properties: [
                        new OA\Property(property: "first_name", type: "string", example: "Juan"),
                        new OA\Property(property: "last_name", type: "string", example: "Pérez"),
                        new OA\Property(property: "address_line_1", type: "string", example: "Av. Principal 123"),
                        new OA\Property(property: "city", type: "string", example: "Caracas"),
                        new OA\Property(property: "country", type: "string", example: "Venezuela")
                    ]
                ),
                new OA\Property(property: "notes", type: "string", nullable: true, example: "Entregar en horario de oficina"),
                new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2024-01-15T10:00:00Z"),
                new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2024-01-15T11:30:00Z"),
                new OA\Property(property: "paid_at", type: "string", format: "date-time", nullable: true, example: null),
                new OA\Property(property: "shipped_at", type: "string", format: "date-time", nullable: true, example: null),
                new OA\Property(property: "delivered_at", type: "string", format: "date-time", nullable: true, example: null),
                new OA\Property(property: "cancelled_at", type: "string", format: "date-time", nullable: true, example: null),
                new OA\Property(
                    property: "items",
                    type: "array",
                    items: new OA\Items(
                        type: "object",
                        properties: [
                            new OA\Property(property: "id", type: "integer", example: 1),
                            new OA\Property(property: "product_id", type: "integer", example: 5),
                            new OA\Property(property: "quantity", type: "integer", example: 2),
                            new OA\Property(property: "price", type: "number", format: "float", example: 99.99),
                            new OA\Property(property: "total", type: "number", format: "float", example: 199.98),
                            new OA\Property(property: "product_name", type: "string", example: "iPhone 14 Pro"),
                            new OA\Property(property: "product_sku", type: "string", example: "IPH14P-256-BLK"),
                            new OA\Property(property: "product_image", type: "string", nullable: true, example: "https://example.com/images/iphone.jpg"),
                            new OA\Property(property: "variant_description", type: "string", nullable: true, example: "Color: Negro, Storage: 256GB")
                        ]
                    )
                ),
                new OA\Property(
                    property: "latest_status_change",
                    type: "object",
                    nullable: true,
                    properties: [
                        new OA\Property(property: "previous_status", type: "string", nullable: true, example: null),
                        new OA\Property(property: "new_status", type: "string", example: "pending"),
                        new OA\Property(property: "notes", type: "string", nullable: true, example: "Pedido creado"),
                        new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2024-01-15T10:00:00Z")
                    ]
                )
            ]
        )
    ]
)]
class OrderResponseSchema {}
