<?php

namespace App\OpenApi\Schemas;

/**
 * @OA\Schema(
 *     schema="StoreCreateRequest",
 *     type="object",
 *     required={"name"},
 *     @OA\Property(property="name", type="string", example="My Store"),
 *     @OA\Property(
 *         property="config",
 *         type="object",
 *         @OA\Property(property="products", type="boolean", example=true),
 *         @OA\Property(property="categories", type="boolean", example=true),
 *         @OA\Property(property="cupons", type="boolean", example=false),
 *         @OA\Property(property="gifcards", type="boolean", example=false),
 *         @OA\Property(property="wishlist", type="boolean", example=false),
 *         @OA\Property(property="reviews", type="boolean", example=false),
 *         @OA\Property(
 *             property="notifications",
 *             type="object",
 *             @OA\Property(property="emails", type="boolean", example=true),
 *             @OA\Property(property="telegram", type="boolean", example=false)
 *         )
 *     )
 * )
 */
class StoreCreateRequestSchema {}

