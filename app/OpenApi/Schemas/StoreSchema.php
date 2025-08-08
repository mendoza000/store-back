<?php

namespace App\OpenApi\Schemas;

/**
 * @OA\Schema(
 *     schema="Store",
 *     type="object",
 *     required={"id", "name"},
 *     @OA\Property(property="id", type="string", format="uuid", example="b3b8c7e2-1c2d-4e5f-8a6b-123456789abc"),
 *     @OA\Property(property="name", type="string", example="My Store"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(property="config", ref="#/components/schemas/StoreConfig"),
 * )
 */
class StoreSchema {}

