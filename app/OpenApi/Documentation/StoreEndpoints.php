<?php

namespace App\OpenApi\Documentation;

/**
 * @see \App\Http\Controllers\Api\V1\StoreController
 *
 * @OA\Tag(
 *     name="Store",
 *     description="Store management"
 * )
 *
 * @OA\Get(
 *     path="/api/v1/store",
 *     tags={"Store"},
 *     summary="Get all stores",
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
 *     tags={"Store"},
 *     summary="Create a new store with optional config",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/StoreCreateRequest")
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Store created",
 *         @OA\JsonContent(ref="#/components/schemas/Store")
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/v1/store/{id}",
 *     tags={"Store"},
 *     summary="Get a store by ID",
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
 *     tags={"Store"},
 *     summary="Update a store and its config",
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
 *     @OA\Response(response=404, description="Not found")
 * )
 *
 * @OA\Delete(
 *     path="/api/v1/store/{id}",
 *     tags={"Store"},
 *     summary="Soft delete a store",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response=204,
 *         description="Store deleted"
 *     ),
 *     @OA\Response(response=404, description="Not found")
 * )
 */
class StoreEndpoints {}

