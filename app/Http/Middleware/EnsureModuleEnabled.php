<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\ModuleService;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class EnsureModuleEnabled
{
    public function __construct(
        private readonly ModuleService $moduleService
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next, string $module, ?string $feature = null): ResponseAlias
    {
        if (!$this->moduleService->isModuleEnabled($module)) {
            return $this->createModuleDisabledResponse($module);
        }

        if ($feature !== null && !$this->moduleService->isFeatureEnabled($module, $feature)) {
            return $this->createFeatureDisabledResponse($module, $feature);
        }

        return $next($request);
    }

    /**
     * Create response when module is disabled
     */
    private function createModuleDisabledResponse(string $module): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error' => [
                'code' => 'MODULE_DISABLED',
                'message' => "The {$module} module is not available",
                'details' => [
                    'module' => $module,
                    'enabled_modules' => $this->moduleService->getEnabledModules()
                ]
            ]
        ], Response::HTTP_NOT_FOUND);
    }

    /**
     * Create response when feature is disabled
     */
    private function createFeatureDisabledResponse(string $module, string $feature): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error' => [
                'code' => 'FEATURE_DISABLED',
                'message' => "The {$feature} feature in {$module} module is not available",
                'details' => [
                    'module' => $module,
                    'feature' => $feature,
                    'available_features' => array_keys(array_filter(
                        $this->moduleService->getModuleConfig($module)['features'] ?? []
                    ))
                ]
            ]
        ], Response::HTTP_NOT_FOUND);
    }
}
