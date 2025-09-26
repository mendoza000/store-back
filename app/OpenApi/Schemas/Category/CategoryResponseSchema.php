<?php

declare(strict_types=1);

namespace App\OpenApi\Schemas\Category;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "CategoryResponse",
    type: "object",
    description: "Respuesta exitosa con datos de categoría",
    properties: [
        new OA\Property(property: "success", type: "boolean", example: true, description: "Indica si la operación fue exitosa"),
        new OA\Property(property: "data", ref: "#/components/schemas/Category", description: "Datos de la categoría"),
        new OA\Property(property: "message", type: "string", nullable: true, example: "Category created successfully", description: "Mensaje descriptivo de la operación")
    ]
)]
class CategoryResponseSchema {} 