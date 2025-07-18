<?php

declare(strict_types=1);

namespace App\OpenApi\Traits;

use OpenApi\Attributes as OA;

trait CommonResponsesTrait
{
    /**
     * Respuesta de éxito estándar
     */
    protected function successResponse(string $description = "Operación exitosa"): OA\Response
    {
        return new OA\Response(
            response: 200,
            description: $description,
            content: new OA\JsonContent(ref: "#/components/schemas/ApiResponse")
        );
    }

    /**
     * Respuesta de creación exitosa
     */
    protected function createdResponse(string $description = "Recurso creado exitosamente"): OA\Response
    {
        return new OA\Response(
            response: 201,
            description: $description,
            content: new OA\JsonContent(ref: "#/components/schemas/ApiResponse")
        );
    }

    /**
     * Respuesta de error de validación
     */
    protected function validationErrorResponse(): OA\Response
    {
        return new OA\Response(
            response: 422,
            description: "Error de validación",
            content: new OA\JsonContent(ref: "#/components/schemas/ValidationErrorResponse")
        );
    }

    /**
     * Respuesta de no autenticado
     */
    protected function unauthorizedResponse(): OA\Response
    {
        return new OA\Response(
            response: 401,
            description: "No autenticado",
            content: new OA\JsonContent(ref: "#/components/schemas/UnauthorizedResponse")
        );
    }

    /**
     * Respuesta de no autorizado
     */
    protected function forbiddenResponse(): OA\Response
    {
        return new OA\Response(
            response: 403,
            description: "Sin permisos suficientes",
            content: new OA\JsonContent(ref: "#/components/schemas/ForbiddenResponse")
        );
    }

    /**
     * Respuesta de recurso no encontrado
     */
    protected function notFoundResponse(): OA\Response
    {
        return new OA\Response(
            response: 404,
            description: "Recurso no encontrado",
            content: new OA\JsonContent(ref: "#/components/schemas/NotFoundResponse")
        );
    }

    /**
     * Respuesta de error del servidor
     */
    protected function serverErrorResponse(): OA\Response
    {
        return new OA\Response(
            response: 500,
            description: "Error interno del servidor",
            content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
        );
    }

    /**
     * Security requirement para Sanctum
     */
    protected function sanctumSecurity(): array
    {
        return [["sanctum" => []]];
    }

    /**
     * Request body JSON estándar
     */
    protected function jsonRequestBody(string $schemaRef, bool $required = true): OA\RequestBody
    {
        return new OA\RequestBody(
            required: $required,
            content: new OA\JsonContent(ref: $schemaRef)
        );
    }
}
