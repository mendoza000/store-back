<?php

declare(strict_types=1);

namespace App\OpenApi\Documentation;

use OpenApi\Attributes as OA;

/**
 * Documentación de endpoints del carrito de compras
 */
class CartEndpoints
{
    #[OA\Get(
        path: "/api/v1/cart",
        summary: "Obtener carrito actual",
        description: "Obtiene el carrito actual del usuario autenticado o guest",
        tags: ["Cart"],
        parameters: [
            new OA\Parameter(
                name: "X-Session-ID",
                description: "ID de sesión para usuarios guest",
                in: "header",
                required: false,
                schema: new OA\Schema(type: "string", format: "uuid")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Carrito obtenido exitosamente",
                content: new OA\JsonContent(ref: "#/components/schemas/CartResponse")
            ),
            new OA\Response(
                response: 500,
                description: "Error interno del servidor",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            )
        ]
    )]
    public function index() {}

    #[OA\Post(
        path: "/api/v1/cart/items",
        summary: "Agregar producto al carrito",
        description: "Agrega un producto al carrito del usuario o guest",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/AddToCartRequest")
        ),
        tags: ["Cart"],
        parameters: [
            new OA\Parameter(
                name: "X-Session-ID",
                description: "ID de sesión para usuarios guest",
                in: "header",
                required: false,
                schema: new OA\Schema(type: "string", format: "uuid")
            )
        ],
        responses: [
            new OA\Response(
                response: 201,
                description: "Producto agregado al carrito exitosamente",
                content: new OA\JsonContent(ref: "#/components/schemas/CartResponse")
            ),
            new OA\Response(
                response: 422,
                description: "Error de validación",
                content: new OA\JsonContent(ref: "#/components/schemas/ValidationErrorResponse")
            ),
            new OA\Response(
                response: 500,
                description: "Error interno del servidor",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            )
        ]
    )]
    public function store() {}

    #[OA\Put(
        path: "/api/v1/cart/items/{item}",
        summary: "Actualizar cantidad de item",
        description: "Actualiza la cantidad de un item específico en el carrito",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/UpdateCartItemRequest")
        ),
        tags: ["Cart"],
        parameters: [
            new OA\Parameter(
                name: "item",
                description: "ID del item del carrito",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            ),
            new OA\Parameter(
                name: "X-Session-ID",
                description: "ID de sesión para usuarios guest",
                in: "header",
                required: false,
                schema: new OA\Schema(type: "string", format: "uuid")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Cantidad actualizada exitosamente",
                content: new OA\JsonContent(ref: "#/components/schemas/CartResponse")
            ),
            new OA\Response(
                response: 404,
                description: "Item no encontrado en el carrito",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            ),
            new OA\Response(
                response: 422,
                description: "Error de validación",
                content: new OA\JsonContent(ref: "#/components/schemas/ValidationErrorResponse")
            ),
            new OA\Response(
                response: 500,
                description: "Error interno del servidor",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            )
        ]
    )]
    public function update() {}

    #[OA\Delete(
        path: "/api/v1/cart/items/{item}",
        summary: "Remover item del carrito",
        description: "Elimina un item específico del carrito",
        tags: ["Cart"],
        parameters: [
            new OA\Parameter(
                name: "item",
                description: "ID del item del carrito",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            ),
            new OA\Parameter(
                name: "X-Session-ID",
                description: "ID de sesión para usuarios guest",
                in: "header",
                required: false,
                schema: new OA\Schema(type: "string", format: "uuid")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Producto removido del carrito exitosamente",
                content: new OA\JsonContent(ref: "#/components/schemas/CartResponse")
            ),
            new OA\Response(
                response: 404,
                description: "Item no encontrado en el carrito",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            ),
            new OA\Response(
                response: 500,
                description: "Error interno del servidor",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            )
        ]
    )]
    public function destroy() {}

    #[OA\Delete(
        path: "/api/v1/cart",
        summary: "Vaciar carrito",
        description: "Elimina todos los items del carrito",
        tags: ["Cart"],
        parameters: [
            new OA\Parameter(
                name: "X-Session-ID",
                description: "ID de sesión para usuarios guest",
                in: "header",
                required: false,
                schema: new OA\Schema(type: "string", format: "uuid")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Carrito vaciado exitosamente",
                content: new OA\JsonContent(ref: "#/components/schemas/CartResponse")
            ),
            new OA\Response(
                response: 500,
                description: "Error interno del servidor",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            )
        ]
    )]
    public function clear() {}

    #[OA\Post(
        path: "/api/v1/cart/merge",
        summary: "Fusionar carritos",
        description: "Fusiona el carrito de un usuario guest con el carrito del usuario autenticado",
        security: [["sanctum" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/MergeCartRequest")
        ),
        tags: ["Cart"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Carritos fusionados exitosamente",
                content: new OA\JsonContent(ref: "#/components/schemas/CartResponse")
            ),
            new OA\Response(
                response: 401,
                description: "Usuario no autenticado",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            ),
            new OA\Response(
                response: 404,
                description: "Carrito de invitado no encontrado",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            ),
            new OA\Response(
                response: 422,
                description: "Error de validación",
                content: new OA\JsonContent(ref: "#/components/schemas/ValidationErrorResponse")
            ),
            new OA\Response(
                response: 500,
                description: "Error interno del servidor",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            )
        ]
    )]
    public function merge() {}
}
