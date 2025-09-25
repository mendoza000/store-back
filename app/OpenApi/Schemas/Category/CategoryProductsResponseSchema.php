<?php

declare(strict_types=1);

namespace App\OpenApi\Schemas\Category;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "CategoryProductsResponse",
    type: "object",
    description: "Respuesta con categoría y sus productos paginados",
    properties: [
        new OA\Property(property: "success", type: "boolean", example: true, description: "Indica si la operación fue exitosa"),
        new OA\Property(
            property: "data",
            type: "object",
            properties: [
                new OA\Property(property: "category", ref: "#/components/schemas/Category", description: "Datos de la categoría"),
                new OA\Property(
                    property: "products",
                    type: "array",
                    items: new OA\Items(ref: "#/components/schemas/Product"),
                    description: "Lista de productos de la categoría"
                ),
                new OA\Property(
                    property: "pagination",
                    type: "object",
                    properties: [
                        new OA\Property(property: "current_page", type: "integer", example: 1, description: "Página actual"),
                        new OA\Property(property: "last_page", type: "integer", example: 5, description: "Última página"),
                        new OA\Property(property: "per_page", type: "integer", example: 15, description: "Elementos por página"),
                        new OA\Property(property: "total", type: "integer", example: 67, description: "Total de elementos"),
                        new OA\Property(property: "from", type: "integer", example: 1, description: "Primer elemento de la página"),
                        new OA\Property(property: "to", type: "integer", example: 15, description: "Último elemento de la página")
                    ],
                    description: "Información de paginación"
                )
            ],
            description: "Datos de la respuesta"
        )
    ]
)]
class CategoryProductsResponseSchema {} 