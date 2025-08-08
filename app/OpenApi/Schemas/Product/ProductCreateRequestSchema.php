<?php

declare(strict_types=1);

namespace App\OpenApi\Schemas\Product;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "ProductCreateRequest",
    type: "object",
    description: "Datos requeridos para crear un producto",
    required: [
        "name",
        "slug",
        "price",
        "track_quantity",
        "sku",
        "status",
        "category_id"
    ],
    properties: [
        new OA\Property(property: "name", type: "string", maxLength: 255, example: "Camiseta básica unisex"),
        new OA\Property(property: "slug", type: "string", maxLength: 255, example: "camiseta-basica-unisex"),
        new OA\Property(property: "description", type: "string", nullable: true, example: "Camiseta de algodón 100% con corte clásico."),
        new OA\Property(property: "short_description", type: "string", nullable: true, maxLength: 500, example: "Camiseta de algodón de alta calidad"),
        new OA\Property(property: "price", type: "number", format: "float", minimum: 0, example: 19.99),
        new OA\Property(property: "compare_price", type: "number", format: "float", minimum: 0, nullable: true, example: 24.99),
        new OA\Property(property: "cost_price", type: "number", format: "float", minimum: 0, nullable: true, example: 9.50),
        new OA\Property(property: "track_quantity", type: "integer", minimum: 0, example: 1),
        new OA\Property(property: "sku", type: "string", maxLength: 100, example: "TSHIRT-UNISEX-001"),
        new OA\Property(property: "status", type: "string", enum: ["active", "inactive", "out_of_stock"], example: "active"),
        new OA\Property(property: "category_id", type: "integer", example: 1)
    ]
)]
class ProductCreateRequestSchema {}
