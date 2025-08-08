<?php

declare(strict_types=1);

namespace App\OpenApi\Documentation;

use OpenApi\Attributes as OA;

/**
 * Documentación de endpoints de productos
 */
class ProductsEndpoints
{
    #[OA\Get(
        path: "/api/v1/products",
        summary: "Listar productos",
        description: "Lista paginada de productos del tenant actual",
        tags: ["Products"],
        parameters: [
            new OA\Parameter(ref: "#/components/parameters/X-Store-Id"),
            new OA\Parameter(name: "page", in: "query", required: false, schema: new OA\Schema(type: "integer", minimum: 1, example: 1)),
            new OA\Parameter(name: "per_page", in: "query", required: false, schema: new OA\Schema(type: "integer", minimum: 1, maximum: 100, example: 15))
        ],
        responses: [
            new OA\Response(response: 200, description: "Lista de productos"),
        ]
    )]
    public function index() {}

    #[OA\Get(
        path: "/api/v1/products/{product}",
        summary: "Obtener detalle de producto",
        description: "Devuelve el detalle de un producto por ID",
        tags: ["Products"],
        parameters: [
            new OA\Parameter(ref: "#/components/parameters/X-Store-Id"),
            new OA\Parameter(
                name: "product",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "string")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Detalle de producto"),
            new OA\Response(response: 404, description: "No encontrado")
        ]
    )]
    public function show() {}

    #[OA\Post(
        path: "/api/v1/products",
        summary: "Crear producto",
        description: "Crea un nuevo producto para el tenant actual",
        tags: ["Products"],
        parameters: [
            new OA\Parameter(ref: "#/components/parameters/X-Store-Id")
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/ProductCreateRequest")
        ),
        responses: [
            new OA\Response(response: 201, description: "Producto creado"),
            new OA\Response(response: 422, description: "Error de validación")
        ]
    )]
    public function store() {}

    #[OA\Put(
        path: "/api/v1/products/{product}",
        summary: "Actualizar producto",
        description: "Actualiza un producto existente",
        tags: ["Products"],
        parameters: [
            new OA\Parameter(ref: "#/components/parameters/X-Store-Id"),
            new OA\Parameter(
                name: "product",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "string")
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/ProductUpdateRequest")
        ),
        responses: [
            new OA\Response(response: 200, description: "Producto actualizado"),
            new OA\Response(response: 404, description: "No encontrado"),
            new OA\Response(response: 422, description: "Error de validación")
        ]
    )]
    public function update() {}

    #[OA\Delete(
        path: "/api/v1/products/{product}",
        summary: "Eliminar producto",
        description: "Elimina (soft/hard según implementación) un producto",
        tags: ["Products"],
        parameters: [
            new OA\Parameter(ref: "#/components/parameters/X-Store-Id"),
            new OA\Parameter(
                name: "product",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "string")
            )
        ],
        responses: [
            new OA\Response(response: 204, description: "Producto eliminado"),
            new OA\Response(response: 404, description: "No encontrado")
        ]
    )]
    public function destroy() {}
}
