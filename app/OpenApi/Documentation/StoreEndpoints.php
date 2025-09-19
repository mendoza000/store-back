<?php

namespace App\OpenApi\Documentation;

/**
 * @see \App\Http\Controllers\Api\V1\StoreController
 *
 * SEGURIDAD:
 * - GET endpoints: Públicos (sin autenticación)
 * - POST/PUT/DELETE endpoints: Requieren autenticación y rol de admin
 *
 * @OA\Tag(
 *     name="Store",
 *     description="Store management"
 * )
 *
 * @OA\Get(
 *     path="/api/v1/store",
 *     tags={"Store"},
 *     summary="Get all stores (Público)",
 *     description="Lista todas las tiendas disponibles. Endpoint público, no requiere autenticación.",
 *     @OA\Parameter(
 *         name="include",
 *         in="query",
 *         description="Include related config",
 *         required=false,
 *         @OA\Schema(type="string", example="config")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="List of stores",
 *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Store"))
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/v1/store",
 *     tags={"Store", "Admin"},
 *     summary="Create a new store (Solo Admin)",
 *     description="Crea una nueva tienda con configuración opcional. REQUIERE: Autenticación + Rol Admin",
 *     security={{"sanctum": {}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/StoreCreateRequest")
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Store created",
 *         @OA\JsonContent(ref="#/components/schemas/Store")
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="No autenticado"
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Permisos insuficientes - Se requiere rol admin"
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Error de validación"
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/v1/store/{id}",
 *     tags={"Store"},
 *     summary="Get a store by ID (Público)",
 *     description="Obtiene los detalles de una tienda específica. Endpoint público, no requiere autenticación.",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *         name="include",
 *         in="query",
 *         description="Include related config",
 *         required=false,
 *         @OA\Schema(type="string", example="config")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Store details",
 *         @OA\JsonContent(ref="#/components/schemas/Store")
 *     ),
 *     @OA\Response(response=404, description="Not found")
 * )
 *
 * @OA\Put(
 *     path="/api/v1/store/{id}",
 *     tags={"Store", "Admin"},
 *     summary="Update a store (Solo Admin)",
 *     description="Actualiza una tienda y su configuración. REQUIERE: Autenticación + Rol Admin",
 *     security={{"sanctum": {}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/StoreUpdateRequest")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Store updated",
 *         @OA\JsonContent(ref="#/components/schemas/Store")
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="No autenticado"
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Permisos insuficientes - Se requiere rol admin"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Store not found"
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Error de validación"
 *     )
 * )
 *
 * @OA\Delete(
 *     path="/api/v1/store/{id}",
 *     tags={"Store", "Admin"},
 *     summary="Delete a store (Solo Admin)",
 *     description="Elimina una tienda específica. REQUIERE: Autenticación + Rol Admin",
 *     security={{"sanctum": {}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response=204,
 *         description="Store deleted successfully"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="No autenticado"
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Permisos insuficientes - Se requiere rol admin"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Store not found"
 *     )
 * )
 */
class StoreEndpoints
{
}

