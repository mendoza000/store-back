<?php

namespace App\Http\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Trait para respuestas JSON estandarizadas de la API
 * 
 * Proporciona métodos consistentes para formatear todas las respuestas
 * de la API siguiendo el estándar definido en la documentación.
 */
trait ApiResponseTrait
{
    /**
     * Respuesta exitosa estándar
     */
    protected function successResponse($data = null, string $message = 'Operation completed successfully', int $statusCode = 200, array $meta = []): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message,
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        if (!empty($meta)) {
            $response['meta'] = $meta;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Respuesta con datos paginados
     */
    protected function paginatedResponse(LengthAwarePaginator $paginator, string $message = 'Data retrieved successfully'): JsonResponse
    {
        $meta = [
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
                'has_previous' => $paginator->previousPageUrl() !== null,
                'has_next' => $paginator->nextPageUrl() !== null,
            ]
        ];

        return $this->successResponse(
            $paginator->items(),
            $message,
            200,
            $meta
        );
    }

    /**
     * Respuesta con Resource paginado
     */
    protected function paginatedResourceResponse($resource, string $message = 'Data retrieved successfully'): JsonResponse
    {
        if ($resource instanceof JsonResource) {
            $response = $resource->response()->getData();

            $formattedResponse = [
                'success' => true,
                'message' => $message,
                'data' => $response->data ?? $response,
            ];

            if (isset($response->meta)) {
                $formattedResponse['meta'] = [
                    'pagination' => $response->meta
                ];
            }

            if (isset($response->links)) {
                $formattedResponse['meta']['links'] = $response->links;
            }

            return response()->json($formattedResponse);
        }

        return $this->successResponse($resource, $message);
    }

    /**
     * Respuesta de error estándar
     */
    protected function errorResponse(string $code, string $message, int $statusCode = 400, array $details = []): JsonResponse
    {
        $response = [
            'success' => false,
            'error' => [
                'code' => $code,
                'message' => $message,
            ]
        ];

        if (!empty($details)) {
            $response['error']['details'] = $details;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Respuesta de error de validación (422)
     */
    protected function validationErrorResponse(array $errors, string $message = 'The given data was invalid'): JsonResponse
    {
        return $this->errorResponse(
            'VALIDATION_ERROR',
            $message,
            422,
            ['validation_errors' => $errors]
        );
    }

    /**
     * Respuesta de error de autenticación (401)
     */
    protected function unauthenticatedResponse(string $message = 'Authentication required'): JsonResponse
    {
        return $this->errorResponse(
            'UNAUTHENTICATED',
            $message,
            401
        );
    }

    /**
     * Respuesta de error de autorización (403)
     */
    protected function forbiddenResponse(string $message = 'Insufficient permissions'): JsonResponse
    {
        return $this->errorResponse(
            'FORBIDDEN',
            $message,
            403
        );
    }

    /**
     * Respuesta de recurso no encontrado (404)
     */
    protected function notFoundResponse(string $message = 'Resource not found'): JsonResponse
    {
        return $this->errorResponse(
            'NOT_FOUND',
            $message,
            404
        );
    }

    /**
     * Respuesta de error de conflicto (409)
     */
    protected function conflictResponse(string $message = 'Resource conflict', array $details = []): JsonResponse
    {
        return $this->errorResponse(
            'CONFLICT',
            $message,
            409,
            $details
        );
    }

    /**
     * Respuesta de error interno del servidor (500)
     */
    protected function internalErrorResponse(string $message = 'Internal server error', array $details = []): JsonResponse
    {
        return $this->errorResponse(
            'INTERNAL_SERVER_ERROR',
            $message,
            500,
            app()->environment('local') ? $details : []
        );
    }

    /**
     * Respuesta para recurso creado exitosamente (201)
     */
    protected function createdResponse($data = null, string $message = 'Resource created successfully'): JsonResponse
    {
        return $this->successResponse($data, $message, 201);
    }

    /**
     * Respuesta para recurso actualizado exitosamente (200)
     */
    protected function updatedResponse($data = null, string $message = 'Resource updated successfully'): JsonResponse
    {
        return $this->successResponse($data, $message, 200);
    }

    /**
     * Respuesta para recurso eliminado exitosamente (200)
     */
    protected function deletedResponse(string $message = 'Resource deleted successfully'): JsonResponse
    {
        return $this->successResponse(null, $message, 200);
    }

    /**
     * Respuesta sin contenido (204)
     */
    protected function noContentResponse(): JsonResponse
    {
        return response()->json(null, 204);
    }

    /**
     * Respuesta para operación aceptada pero pendiente (202)
     */
    protected function acceptedResponse($data = null, string $message = 'Request accepted for processing'): JsonResponse
    {
        return $this->successResponse($data, $message, 202);
    }
}
