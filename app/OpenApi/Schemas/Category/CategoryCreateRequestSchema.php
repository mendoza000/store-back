<?php

declare(strict_types=1);

namespace App\OpenApi\Schemas\Category;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "CategoryCreateRequest",
    type: "object",
    description: "Datos requeridos para crear una categoría",
    required: [
        "name",
        "status"
    ],
    properties: [
        new OA\Property(property: "name", type: "string", maxLength: 255, example: "Electrónicos", description: "Nombre de la categoría"),
        new OA\Property(property: "slug", type: "string", maxLength: 255, nullable: true, pattern: "^[a-z0-9]+(?:-[a-z0-9]+)*$", example: "electronicos", description: "Slug único para la categoría. Si no se proporciona, se genera automáticamente"),
        new OA\Property(property: "description", type: "string", nullable: true, example: "Categoría para productos electrónicos y tecnológicos", description: "Descripción detallada de la categoría"),
        new OA\Property(property: "image", type: "string", nullable: true, example: "categories/electronics.jpg", description: "Ruta de la imagen de la categoría"),
        new OA\Property(property: "status", type: "string", enum: ["active", "inactive"], example: "active", description: "Estado de la categoría"),
        new OA\Property(property: "sort_order", type: "integer", minimum: 0, nullable: true, example: 1, description: "Orden de visualización de la categoría")
    ]
)]
class CategoryCreateRequestSchema {} 