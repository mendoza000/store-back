<?php

namespace App\OpenApi\Schemas;

/**
 * @OA\Schema(
 *     schema="StoreConfig",
 *     type="object",
 *     required={"products", "categories", "cupons", "gifcards", "wishlist", "reviews", "notifications"},
 *     @OA\Property(property="id", type="string", example="b3b8c7e2-2231-4e5f-8a6b-123123123123"),
 *     @OA\Property(property="store_id", type="string", format="uuid", example="b3b8c7e2-1c2d-4e5f-8a6b-123456789abc"),
 *     @OA\Property(property="products", type="boolean", example=true),
 *     @OA\Property(property="categories", type="boolean", example=true),
 *     @OA\Property(property="cupons", type="boolean", example=false),
 *     @OA\Property(property="gifcards", type="boolean", example=false),
 *     @OA\Property(property="wishlist", type="boolean", example=false),
 *     @OA\Property(property="reviews", type="boolean", example=false),
 *     @OA\Property(
 *         property="notifications",
 *         type="object",
 *         @OA\Property(property="emails", type="boolean", example=true),
 *         @OA\Property(property="telegram", type="boolean", example=false)
 *     ),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class StoreConfigSchema {}

