<?php

declare(strict_types=1);

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Parameter(
    parameter: "X-Store-Id",
    name: "X-Store-Id",
    description: "UUID de la tienda (tenant). Alternativamente puede usarse 'Store-Id'",
    in: "header",
    required: true,
    schema: new OA\Schema(type: "string", format: "uuid", example: "b3b8c7e2-1c2d-4e5f-8a6b-123456789abc")
)]
class TenantHeader {}
