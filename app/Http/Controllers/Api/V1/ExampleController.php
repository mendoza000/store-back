<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

/**
 * @OA\Tag(
 *     name="Example",
 *     description="Endpoints de ejemplo para demostrar la documentación API"
 * )
 */
class ExampleController extends Controller
{
    use ApiResponseTrait;

    /**
     * @OA\Get(
     *     path="/api/v1/example",
     *     operationId="getExamples",
     *     tags={"Example"},
     *     summary="Obtener lista de ejemplos",
     *     description="Endpoint de ejemplo que demuestra una respuesta exitosa con datos paginados",
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Número de página",
     *         required=false,
     *         @OA\Schema(type="integer", minimum=1, example=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Elementos por página",
     *         required=false,
     *         @OA\Schema(type="integer", minimum=1, maximum=100, example=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de ejemplos obtenida exitosamente",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="data",
     *                         type="array",
     *                         @OA\Items(
     *                             @OA\Property(property="id", type="integer", example=1),
     *                             @OA\Property(property="name", type="string", example="Ejemplo 1"),
     *                             @OA\Property(property="description", type="string", example="Descripción del ejemplo"),
     *                             @OA\Property(property="created_at", type="string", format="date-time")
     *                         )
     *                     )
     *                 )
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        // Validar parámetros de consulta
        $request->validate([
            'page' => 'integer|min:1',
            'per_page' => 'integer|min:1|max:100',
        ]);

        // Datos de ejemplo
        $examples = [
            ['id' => 1, 'name' => 'Ejemplo 1', 'description' => 'Primer ejemplo', 'created_at' => now()],
            ['id' => 2, 'name' => 'Ejemplo 2', 'description' => 'Segundo ejemplo', 'created_at' => now()],
            ['id' => 3, 'name' => 'Ejemplo 3', 'description' => 'Tercer ejemplo', 'created_at' => now()],
        ];

        return $this->successResponse($examples, 'Ejemplos obtenidos exitosamente');
    }

    /**
     * @OA\Get(
     *     path="/api/v1/example/{id}",
     *     operationId="getExample",
     *     tags={"Example"},
     *     summary="Obtener un ejemplo específico",
     *     description="Endpoint de ejemplo que demuestra obtener un recurso específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del ejemplo",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ejemplo obtenido exitosamente",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="data",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="Ejemplo 1"),
     *                         @OA\Property(property="description", type="string", example="Descripción del ejemplo"),
     *                         @OA\Property(property="created_at", type="string", format="date-time")
     *                     )
     *                 )
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Ejemplo no encontrado",
     *         @OA\JsonContent(ref="#/components/schemas/NotFoundResponse")
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        if ($id > 3) {
            return $this->notFoundResponse('Ejemplo no encontrado');
        }

        $example = [
            'id' => $id,
            'name' => "Ejemplo {$id}",
            'description' => "Descripción del ejemplo {$id}",
            'created_at' => now()
        ];

        return $this->successResponse($example, 'Ejemplo obtenido exitosamente');
    }

    /**
     * @OA\Post(
     *     path="/api/v1/example/protected",
     *     operationId="createProtectedExample",
     *     tags={"Example"},
     *     summary="Crear ejemplo protegido",
     *     description="Endpoint de ejemplo que requiere autenticación",
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Datos del ejemplo",
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Nuevo ejemplo"),
     *             @OA\Property(property="description", type="string", example="Descripción del nuevo ejemplo")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Ejemplo creado exitosamente",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="data",
     *                         @OA\Property(property="id", type="integer", example=4),
     *                         @OA\Property(property="name", type="string", example="Nuevo ejemplo"),
     *                         @OA\Property(property="description", type="string", example="Descripción del nuevo ejemplo"),
     *                         @OA\Property(property="created_at", type="string", format="date-time")
     *                     )
     *                 )
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado",
     *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedResponse")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
     *     )
     * )
     */
    public function createProtected(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
        ]);

        $example = [
            'id' => 4,
            'name' => $validated['name'],
            'description' => $validated['description'],
            'created_at' => now(),
            'user_id' => $request->user()->id
        ];

        return $this->createdResponse($example, 'Ejemplo creado exitosamente');
    }

    /**
     * @OA\Post(
     *     path="/api/v1/example/admin",
     *     operationId="createAdminExample",
     *     tags={"Example"},
     *     summary="Crear ejemplo de administrador",
     *     description="Endpoint de ejemplo que requiere rol de administrador",
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Datos del ejemplo de admin",
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Ejemplo admin"),
     *             @OA\Property(property="description", type="string", example="Solo admins pueden crear esto")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Ejemplo de admin creado exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/ApiResponse")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado",
     *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedResponse")
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Sin permisos de administrador",
     *         @OA\JsonContent(ref="#/components/schemas/ForbiddenResponse")
     *     )
     * )
     */
    public function createAdmin(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
        ]);

        $example = [
            'id' => 5,
            'name' => $validated['name'],
            'description' => $validated['description'],
            'type' => 'admin',
            'created_at' => now(),
            'admin_id' => $request->user()->id
        ];

        return $this->createdResponse($example, 'Ejemplo de administrador creado exitosamente');
    }
}
