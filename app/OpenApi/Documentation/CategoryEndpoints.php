<?php

declare(strict_types=1);

namespace App\OpenApi\Documentation;

use OpenApi\Attributes as OA;

/**
 * Documentación de endpoints de categorías
 * 
 * SEGURIDAD:
 * - GET endpoints: Públicos (sin autenticación)
 * - POST/PUT/DELETE endpoints: Requieren autenticación y rol de admin
 */
class CategoryEndpoints
{
    #[OA\Get(
        path: "/api/v1/categories",
        summary: "Listar categorías (Público)",
        description: "Lista paginada de categorías activas del tenant actual. Endpoint público, no requiere autenticación.",
        tags: ["Categories"],
        parameters: [
            new OA\Parameter(ref: "#/components/parameters/X-Store-Id"),
            new OA\Parameter(name: "page", in: "query", required: false, schema: new OA\Schema(type: "integer", minimum: 1, example: 1), description: "Número de página"),
            new OA\Parameter(name: "per_page", in: "query", required: false, schema: new OA\Schema(type: "integer", minimum: 1, maximum: 100, example: 20), description: "Elementos por página")
        ],
        responses: [
            new OA\Response(
                response: 200, 
                description: "Lista de categorías",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(
                            property: "data",
                            type: "array",
                            items: new OA\Items(ref: "#/components/schemas/Category")
                        ),
                        new OA\Property(
                            property: "links",
                            type: "object",
                            description: "Enlaces de paginación"
                        ),
                        new OA\Property(
                            property: "meta",
                            type: "object", 
                            description: "Metadatos de paginación"
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 400, 
                description: "Error en la petición",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            )
        ]
    )]
    public function index() {}

    #[OA\Get(
        path: "/api/v1/categories/{slug}",
        summary: "Obtener categoría por slug (Público)",
        description: "Devuelve el detalle de una categoría por su slug. Endpoint público, no requiere autenticación.",
        tags: ["Categories"],
        parameters: [
            new OA\Parameter(ref: "#/components/parameters/X-Store-Id"),
            new OA\Parameter(
                name: "slug",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "string", example: "electronicos"),
                description: "Slug único de la categoría"
            )
        ],
        responses: [
            new OA\Response(
                response: 200, 
                description: "Detalle de categoría",
                content: new OA\JsonContent(ref: "#/components/schemas/CategoryResponse")
            ),
            new OA\Response(
                response: 404, 
                description: "Categoría no encontrada",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(
                            property: "error",
                            type: "object",
                            properties: [
                                new OA\Property(property: "code", type: "string", example: "CATEGORY_NOT_FOUND"),
                                new OA\Property(property: "message", type: "string", example: "Categoría no encontrada"),
                                new OA\Property(
                                    property: "details",
                                    type: "object",
                                    properties: [
                                        new OA\Property(property: "slug", type: "string", example: "invalid-slug")
                                    ]
                                )
                            ]
                        )
                    ]
                )
            )
        ]
    )]
    public function showBySlug() {}

    #[OA\Get(
        path: "/api/v1/categories/{slug}/products",
        summary: "Obtener productos de una categoría (Público)",
        description: "Lista paginada de productos de una categoría específica con filtros opcionales. Endpoint público, no requiere autenticación.",
        tags: ["Categories", "Products"],
        parameters: [
            new OA\Parameter(ref: "#/components/parameters/X-Store-Id"),
            new OA\Parameter(
                name: "slug",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "string", example: "electronicos"),
                description: "Slug único de la categoría"
            ),
            new OA\Parameter(name: "per_page", in: "query", required: false, schema: new OA\Schema(type: "integer", minimum: 1, maximum: 100, example: 15), description: "Productos por página"),
            new OA\Parameter(name: "status", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["active", "inactive"], example: "active"), description: "Estado de los productos"),
            new OA\Parameter(name: "min_price", in: "query", required: false, schema: new OA\Schema(type: "number", format: "float", minimum: 0), description: "Precio mínimo"),
            new OA\Parameter(name: "max_price", in: "query", required: false, schema: new OA\Schema(type: "number", format: "float", minimum: 0), description: "Precio máximo"),
            new OA\Parameter(name: "search", in: "query", required: false, schema: new OA\Schema(type: "string"), description: "Búsqueda en nombre y descripción"),
            new OA\Parameter(name: "sort_by", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["name", "price", "created_at", "stock"], example: "name"), description: "Campo de ordenamiento"),
            new OA\Parameter(name: "sort_order", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["asc", "desc"], example: "asc"), description: "Dirección del ordenamiento")
        ],
        responses: [
            new OA\Response(
                response: 200, 
                description: "Productos de la categoría",
                content: new OA\JsonContent(ref: "#/components/schemas/CategoryProductsResponse")
            ),
            new OA\Response(
                response: 404, 
                description: "Categoría no encontrada",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(
                            property: "error",
                            type: "object",
                            properties: [
                                new OA\Property(property: "code", type: "string", example: "CATEGORY_NOT_FOUND"),
                                new OA\Property(property: "message", type: "string", example: "Categoría no encontrada"),
                                new OA\Property(
                                    property: "details",
                                    type: "object",
                                    properties: [
                                        new OA\Property(property: "slug", type: "string", example: "invalid-slug")
                                    ]
                                )
                            ]
                        )
                    ]
                )
            )
        ]
    )]
    public function getProductsBySlug() {}

    #[OA\Get(
        path: "/api/v1/categories/{id}",
        summary: "Obtener categoría por ID (Solo Admin)",
        description: "Devuelve el detalle de una categoría por ID. REQUIERE: Autenticación + Rol Admin",
        tags: ["Categories", "Admin"],
        security: [
            ["sanctum" => []]
        ],
        parameters: [
            new OA\Parameter(ref: "#/components/parameters/X-Store-Id"),
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "string", format: "uuid"),
                description: "ID único de la categoría"
            )
        ],
        responses: [
            new OA\Response(
                response: 200, 
                description: "Detalle de categoría",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "data", ref: "#/components/schemas/Category")
                    ]
                )
            ),
            new OA\Response(response: 401, description: "No autenticado"),
            new OA\Response(response: 403, description: "Permisos insuficientes - Se requiere rol admin"),
            new OA\Response(response: 404, description: "Categoría no encontrada")
        ]
    )]
    public function show() {}

    #[OA\Post(
        path: "/api/v1/categories",
        summary: "Crear categoría (Solo Admin)",
        description: "Crea una nueva categoría para el tenant actual. REQUIERE: Autenticación + Rol Admin",
        tags: ["Categories", "Admin"],
        security: [
            ["sanctum" => []]
        ],
        parameters: [
            new OA\Parameter(ref: "#/components/parameters/X-Store-Id")
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/CategoryCreateRequest")
        ),
        responses: [
            new OA\Response(
                response: 201, 
                description: "Categoría creada exitosamente",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Category created successfully"),
                        new OA\Property(property: "data", ref: "#/components/schemas/Category")
                    ]
                )
            ),
            new OA\Response(response: 401, description: "No autenticado"),
            new OA\Response(response: 403, description: "Permisos insuficientes - Se requiere rol admin"),
            new OA\Response(response: 422, description: "Error de validación", content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse"))
        ]
    )]
    public function store() {}

    #[OA\Put(
        path: "/api/v1/categories/{id}",
        summary: "Actualizar categoría (Solo Admin)",
        description: "Actualiza una categoría existente. REQUIERE: Autenticación + Rol Admin",
        tags: ["Categories", "Admin"],
        security: [
            ["sanctum" => []]
        ],
        parameters: [
            new OA\Parameter(ref: "#/components/parameters/X-Store-Id"),
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "string", format: "uuid"),
                description: "ID único de la categoría"
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/CategoryUpdateRequest")
        ),
        responses: [
            new OA\Response(
                response: 200, 
                description: "Categoría actualizada exitosamente",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Category updated successfully"),
                        new OA\Property(property: "data", ref: "#/components/schemas/Category")
                    ]
                )
            ),
            new OA\Response(response: 401, description: "No autenticado"),
            new OA\Response(response: 403, description: "Permisos insuficientes - Se requiere rol admin"),
            new OA\Response(response: 404, description: "Categoría no encontrada"),
            new OA\Response(response: 422, description: "Error de validación", content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse"))
        ]
    )]
    public function update() {}

    #[OA\Delete(
        path: "/api/v1/categories/{id}",
        summary: "Eliminar categoría (Solo Admin)",
        description: "Elimina una categoría. REQUIERE: Autenticación + Rol Admin",
        tags: ["Categories", "Admin"],
        security: [
            ["sanctum" => []]
        ],
        parameters: [
            new OA\Parameter(ref: "#/components/parameters/X-Store-Id"),
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "string", format: "uuid"),
                description: "ID único de la categoría"
            )
        ],
        responses: [
            new OA\Response(
                response: 200, 
                description: "Categoría eliminada exitosamente",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Category deleted successfully")
                    ]
                )
            ),
            new OA\Response(response: 401, description: "No autenticado"),
            new OA\Response(response: 403, description: "Permisos insuficientes - Se requiere rol admin"),
            new OA\Response(response: 404, description: "Categoría no encontrada")
        ]
    )]
    public function destroy() {}
} 