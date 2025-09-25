<?php

declare(strict_types=1);

namespace App\OpenApi\Schemas\Category;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Category",
    type: "object",
    description: "Esquema de una categoría",
    properties: [
        new OA\Property(property: "id", type: "string", format: "uuid", example: "9d2e8c5a-1234-4b6c-8f9e-1a2b3c4d5e6f", description: "ID único de la categoría"),
        new OA\Property(property: "name", type: "string", example: "Electrónicos", description: "Nombre de la categoría"),
        new OA\Property(property: "slug", type: "string", example: "electronicos", description: "Slug único de la categoría"),
        new OA\Property(property: "description", type: "string", nullable: true, example: "Categoría para productos electrónicos y tecnológicos", description: "Descripción de la categoría"),
        new OA\Property(property: "image", type: "string", nullable: true, example: "http://localhost:8000/storage/categories/electronics.jpg", description: "URL completa de la imagen de la categoría"),
        new OA\Property(property: "status", type: "string", enum: ["active", "inactive"], example: "active", description: "Estado de la categoría"),
        new OA\Property(property: "sort_order", type: "integer", nullable: true, example: 1, description: "Orden de visualización")
    ]
)]
class CategorySchema {} 