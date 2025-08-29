<?php

declare(strict_types=1);

namespace App\OpenApi\Schemas\Payment;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "PaymentStatsResponse",
    type: "object",
    description: "Estadísticas de pagos para administradores",
    properties: [
        new OA\Property(property: "success", type: "boolean", example: true),
        new OA\Property(property: "message", type: "string", example: "Estadísticas obtenidas exitosamente"),
        new OA\Property(
            property: "data",
            type: "object",
            properties: [
                new OA\Property(
                    property: "overview",
                    type: "object",
                    description: "Resumen general",
                    properties: [
                        new OA\Property(property: "total_payments", type: "integer", example: 150),
                        new OA\Property(property: "pending_payments", type: "integer", example: 25),
                        new OA\Property(property: "verified_payments", type: "integer", example: 120),
                        new OA\Property(property: "rejected_payments", type: "integer", example: 5),
                        new OA\Property(property: "total_amount", type: "number", format: "float", example: 45750.50),
                        new OA\Property(property: "pending_amount", type: "number", format: "float", example: 3250.00),
                        new OA\Property(property: "verified_amount", type: "number", format: "float", example: 42000.50),
                        new OA\Property(property: "rejected_amount", type: "number", format: "float", example: 500.00)
                    ]
                ),
                new OA\Property(
                    property: "by_payment_method",
                    type: "array",
                    description: "Estadísticas por método de pago",
                    items: new OA\Items(
                        type: "object",
                        properties: [
                            new OA\Property(property: "payment_method_id", type: "integer", example: 1),
                            new OA\Property(property: "payment_method_name", type: "string", example: "Transferencia Bancaria"),
                            new OA\Property(property: "count", type: "integer", example: 75),
                            new OA\Property(property: "total_amount", type: "number", format: "float", example: 22500.50)
                        ]
                    )
                ),
                new OA\Property(
                    property: "by_status",
                    type: "array",
                    description: "Estadísticas por estado",
                    items: new OA\Items(
                        type: "object",
                        properties: [
                            new OA\Property(property: "status", type: "string", example: "verified"),
                            new OA\Property(property: "count", type: "integer", example: 120),
                            new OA\Property(property: "total_amount", type: "number", format: "float", example: 42000.50)
                        ]
                    )
                ),
                new OA\Property(
                    property: "recent_activity",
                    type: "array",
                    description: "Actividad reciente (últimos 7 días)",
                    items: new OA\Items(
                        type: "object",
                        properties: [
                            new OA\Property(property: "date", type: "string", format: "date", example: "2024-01-15"),
                            new OA\Property(property: "payments_count", type: "integer", example: 8),
                            new OA\Property(property: "total_amount", type: "number", format: "float", example: 2400.00)
                        ]
                    )
                )
            ]
        )
    ]
)]
class PaymentStatsResponseSchema {} 