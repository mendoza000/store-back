<?php

declare(strict_types=1);

namespace App\OpenApi\Schemas\Common;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "ErrorResponse",
    type: "object",
    description: "Estructura de respuesta para errores",
    properties: [
        new OA\Property(
            property: "success",
            type: "boolean",
            description: "Siempre false para errores",
            example: false
        ),
        new OA\Property(
            property: "error",
            type: "object",
            properties: [
                new OA\Property(
                    property: "code",
                    type: "string",
                    description: "Código del error",
                    example: "VALIDATION_ERROR"
                ),
                new OA\Property(
                    property: "message",
                    type: "string",
                    description: "Mensaje del error",
                    example: "The given data was invalid"
                ),
                new OA\Property(
                    property: "details",
                    type: "object",
                    description: "Detalles específicos del error (ej: errores de validación)",
                    example: ["email" => ["The email field is required."]]
                )
            ]
        )
    ]
)]
class ErrorResponseSchema
{
    // Esta clase solo existe para contener la definición del esquema
}

#[OA\Schema(
    schema: "ValidationErrorResponse",
    allOf: [
        new OA\Schema(ref: "#/components/schemas/ErrorResponse")
    ],
    description: "Respuesta específica para errores de validación (422)"
)]
class ValidationErrorResponseSchema {}

#[OA\Schema(
    schema: "UnauthorizedResponse",
    allOf: [
        new OA\Schema(ref: "#/components/schemas/ErrorResponse")
    ],
    description: "Respuesta para errores de autenticación (401)"
)]
class UnauthorizedResponseSchema {}

#[OA\Schema(
    schema: "ForbiddenResponse",
    allOf: [
        new OA\Schema(ref: "#/components/schemas/ErrorResponse")
    ],
    description: "Respuesta para errores de autorización (403)"
)]
class ForbiddenResponseSchema {}

#[OA\Schema(
    schema: "NotFoundResponse",
    allOf: [
        new OA\Schema(ref: "#/components/schemas/ErrorResponse")
    ],
    description: "Respuesta para recursos no encontrados (404)"
)]
class NotFoundResponseSchema {}
