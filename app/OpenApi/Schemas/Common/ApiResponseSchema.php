<?php

declare(strict_types=1);

namespace App\OpenApi\Schemas\Common;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "ApiResponse",
    type: "object",
    description: "Estructura estándar de respuesta de la API",
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
            description: "Mensaje descriptivo de la operación",
            example: "Operation completed successfully"
        ),
        new OA\Property(
            property: "data",
            description: "Datos de respuesta (variable según endpoint)"
        ),
        new OA\Property(
            property: "meta",
            type: "object",
            description: "Metadatos adicionales (paginación, totales, etc.)",
            properties: [
                new OA\Property(
                    property: "pagination",
                    type: "object",
                    properties: [
                        new OA\Property(property: "current_page", type: "integer", example: 1),
                        new OA\Property(property: "per_page", type: "integer", example: 15),
                        new OA\Property(property: "total", type: "integer", example: 150),
                        new OA\Property(property: "last_page", type: "integer", example: 10)
                    ]
                )
            ]
        )
    ]
)]
class ApiResponseSchema
{
    // Esta clase solo existe para contener la definición del esquema
}
