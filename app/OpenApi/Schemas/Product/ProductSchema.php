<?php

declare(strict_types=1);

namespace App\OpenApi\Schemas\Product;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Product",
    type: "object",
    description: "Esquema de un producto",
    properties: [
        new OA\Property(property: "id", type: "string", format: "uuid", example: "9d2e8c5a-1234-4b6c-8f9e-1a2b3c4d5e6f", description: "ID único del producto"),
        new OA\Property(property: "name", type: "string", example: "Smartphone Galaxy S23", description: "Nombre del producto"),
        new OA\Property(property: "slug", type: "string", example: "smartphone-galaxy-s23", description: "Slug único del producto"),
        new OA\Property(property: "description", type: "string", nullable: true, example: "Smartphone con pantalla AMOLED de 6.1 pulgadas...", description: "Descripción completa del producto"),
        new OA\Property(property: "short_description", type: "string", nullable: true, example: "Smartphone de última generación", description: "Descripción corta del producto"),
        new OA\Property(property: "price", type: "number", format: "float", example: 899.99, description: "Precio del producto"),
        new OA\Property(property: "compare_price", type: "number", format: "float", nullable: true, example: 999.99, description: "Precio de comparación/antes"),
        new OA\Property(property: "cost_price", type: "number", format: "float", nullable: true, example: 450.00, description: "Precio de costo"),
        new OA\Property(property: "track_quantity", type: "integer", example: 1, description: "Si se rastrea la cantidad"),
        new OA\Property(property: "sku", type: "string", example: "GAL-S23-128-BLK", description: "Código SKU del producto"),
        new OA\Property(property: "status", type: "string", enum: ["active", "inactive", "out_of_stock"], example: "active", description: "Estado del producto"),
        new OA\Property(property: "category", ref: "#/components/schemas/Category", description: "Categoría del producto"),
        new OA\Property(
            property: "images",
            type: "array",
            items: new OA\Items(
                type: "object",
                properties: [
                    new OA\Property(property: "id", type: "string", format: "uuid", example: "9d2e8c5a-1234-4b6c-8f9e-1a2b3c4d5e6f"),
                    new OA\Property(property: "image_path", type: "string", example: "products/smartphone-galaxy.jpg"),
                    new OA\Property(property: "sort_order", type: "integer", example: 1),
                    new OA\Property(property: "url", type: "string", example: "http://localhost:8000/storage/products/smartphone-galaxy.jpg"),
                    new OA\Property(property: "is_primary", type: "boolean", example: true),
                    new OA\Property(property: "is_active", type: "boolean", example: true),
                    new OA\Property(property: "alt_text", type: "string", nullable: true, example: "Smartphone Galaxy S23"),
                    new OA\Property(property: "title", type: "string", nullable: true, example: "Imagen principal")
                ]
            ),
            description: "Imágenes del producto"
        )
    ]
)]
class ProductSchema {} 