<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

/**
 * Servicio para gestionar la configuración modular de la tienda
 * 
 * Este servicio permite verificar qué módulos y características están habilitados
 * para la tienda actual, implementando un sistema de configuración flexible.
 */
class ModuleService
{
    private const CACHE_KEY = 'store_modules_config';
    private const CACHE_TTL = 3600; // 1 hora

    private ?array $config = null;

    /**
     * Verificar si un módulo está habilitado
     */
    public function isModuleEnabled(string $module): bool
    {
        $config = $this->getConfig();

        if (!isset($config['modules'][$module])) {
            return false;
        }

        $moduleConfig = $config['modules'][$module];
        $isEnabled = $moduleConfig['enabled'] ?? false;

        if (!$isEnabled) {
            return false;
        }

        // Verificar dependencias
        return $this->validateDependencies($module);
    }

    /**
     * Verificar si una característica específica está habilitada
     */
    public function isFeatureEnabled(string $module, string $feature): bool
    {
        if (!$this->isModuleEnabled($module)) {
            return false;
        }

        $config = $this->getConfig();
        return $config['modules'][$module]['features'][$feature] ?? false;
    }

    /**
     * Obtener la configuración de un módulo
     */
    public function getModuleConfig(string $module): ?array
    {
        if (!$this->isModuleEnabled($module)) {
            return null;
        }

        $config = $this->getConfig();
        return $config['modules'][$module] ?? null;
    }

    /**
     * Obtener configuración de la tienda
     */
    public function getStoreConfig(): array
    {
        $config = $this->getConfig();
        return $config['store'] ?? [];
    }

    /**
     * Obtener configuración de un setting específico de un módulo
     */
    public function getModuleSetting(string $module, string $setting, mixed $default = null): mixed
    {
        $moduleConfig = $this->getModuleConfig($module);
        return $moduleConfig['settings'][$setting] ?? $default;
    }

    /**
     * Obtener todos los módulos habilitados
     */
    public function getEnabledModules(): array
    {
        $config = $this->getConfig();
        $enabledModules = [];

        foreach ($config['modules'] as $moduleName => $moduleConfig) {
            if ($this->isModuleEnabled($moduleName)) {
                $enabledModules[] = $moduleName;
            }
        }

        return $enabledModules;
    }

    /**
     * Obtener todas las características habilitadas de un módulo
     */
    public function getEnabledFeatures(string $module): array
    {
        if (!$this->isModuleEnabled($module)) {
            return [];
        }

        $config = $this->getConfig();
        $features = $config['modules'][$module]['features'] ?? [];

        return array_keys(array_filter($features));
    }

    /**
     * Verificar si un feature flag está habilitado
     */
    public function isFeatureFlagEnabled(string $flag): bool
    {
        $config = $this->getConfig();
        return $config['feature_flags'][$flag] ?? false;
    }

    /**
     * Obtener los límites configurados
     */
    public function getLimit(string $limit): mixed
    {
        $config = $this->getConfig();
        return $config['limits'][$limit] ?? null;
    }

    /**
     * Obtener configuración para el API público
     * Solo incluye información que puede ser expuesta al frontend
     */
    public function getPublicConfig(): array
    {
        $config = $this->getConfig();

        return [
            'store' => $config['store'] ?? [],
            'modules' => array_map(function ($module) {
                return [
                    'enabled' => $module['enabled'] ?? false,
                    'features' => array_keys(array_filter($module['features'] ?? [])),
                ];
            }, $config['modules'] ?? []),
            'feature_flags' => array_filter($config['feature_flags'] ?? []),
            'limits' => $config['limits'] ?? [],
        ];
    }

    /**
     * Validar que las dependencias de un módulo estén satisfechas
     */
    public function validateDependencies(string $module): bool
    {
        $config = $this->getConfig();
        $dependencies = $config['dependencies'][$module] ?? [];

        foreach ($dependencies as $dependency) {
            if (
                !isset($config['modules'][$dependency]) ||
                !($config['modules'][$dependency]['enabled'] ?? false)
            ) {

                Log::warning("Module dependency not satisfied", [
                    'module' => $module,
                    'dependency' => $dependency
                ]);

                return false;
            }
        }

        return true;
    }

    /**
     * Limpiar caché de configuración
     */
    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
        $this->config = null;
    }

    /**
     * Obtener la configuración completa (desde caché o archivo)
     */
    private function getConfig(): array
    {
        if ($this->config !== null) {
            return $this->config;
        }

        $this->config = Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return Config::get('modules', []);
        });

        return $this->config;
    }

    /**
     * Verificar si la configuración está válida
     */
    public function validateConfiguration(): array
    {
        $config = $this->getConfig();
        $errors = [];

        // Verificar que existan las secciones principales
        $requiredSections = ['store', 'modules', 'dependencies'];
        foreach ($requiredSections as $section) {
            if (!isset($config[$section])) {
                $errors[] = "Missing required section: {$section}";
            }
        }

        // Verificar módulos core
        $coreModules = ['core'];
        foreach ($coreModules as $module) {
            if (!isset($config['modules'][$module]) || !$config['modules'][$module]['enabled']) {
                $errors[] = "Core module '{$module}' must be enabled";
            }
        }

        // Verificar dependencias
        foreach ($config['dependencies'] ?? [] as $module => $dependencies) {
            foreach ($dependencies as $dependency) {
                if (!isset($config['modules'][$dependency])) {
                    $errors[] = "Module '{$module}' depends on undefined module '{$dependency}'";
                }
            }
        }

        return $errors;
    }

    /**
     * Obtener estadísticas de configuración
     */
    public function getConfigStats(): array
    {
        $config = $this->getConfig();

        $totalModules = count($config['modules'] ?? []);
        $enabledModules = count($this->getEnabledModules());

        $totalFeatures = 0;
        $enabledFeatures = 0;

        foreach ($config['modules'] ?? [] as $module => $moduleConfig) {
            $features = $moduleConfig['features'] ?? [];
            $totalFeatures += count($features);

            if ($this->isModuleEnabled($module)) {
                $enabledFeatures += count(array_filter($features));
            }
        }

        return [
            'total_modules' => $totalModules,
            'enabled_modules' => $enabledModules,
            'total_features' => $totalFeatures,
            'enabled_features' => $enabledFeatures,
            'store_name' => $config['store']['name'] ?? 'Sin nombre',
            'currency' => $config['store']['currency'] ?? 'USD',
        ];
    }
}
