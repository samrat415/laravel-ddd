<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;
use Livewire\Livewire;
use Illuminate\Support\Str;

class FrontendServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Base path for frontend modules
        $path = base_path('frontend');

        // Load portal divisions
        $portalDivisions = glob("$path/*", GLOB_ONLYDIR);

        foreach ($portalDivisions as $portalDivisionPath) {
            $portalDivision = basename($portalDivisionPath);

            // Load modules within each portal division
            $modules = glob("$portalDivisionPath/*", GLOB_ONLYDIR);

            foreach ($modules as $modulePath) {
                $module = basename($modulePath);
                $this->loadModule($portalDivision, $module, $modulePath);
            }
        }
    }

    protected function loadModule($portalDivision, $module, $modulePath)
    {
        // Load routes
        $routesPath = "$modulePath/routes";
        $routeFiles = glob("$routesPath/*.php");
        foreach ($routeFiles as $routeFile) {
            include $routeFile;
        }

        // Load middleware
        $middlewarePath = "$modulePath/Middleware";
        if (is_dir($middlewarePath)) {
            $middlewareFiles = glob("$middlewarePath/*.php");
            foreach ($middlewareFiles as $middlewareFile) {
                $middlewareClass = "Frontend\\$portalDivision\\$module\\Middleware\\" . basename($middlewareFile, '.php');
                $this->app['router']->middleware($middlewareClass);
            }
        }

        // Load views
        if (is_dir("$modulePath/Views")) {
            $this->loadViewsFrom("$modulePath/Views", "$portalDivision.$module");
        }

        // Load middleware groups
        if (is_dir($middlewarePath)) {
            foreach (scandir($middlewarePath) as $file) {
                $filePath = "$middlewarePath/$file";
                if (is_file($filePath)) {
                    $middleware = pathinfo($file, PATHINFO_FILENAME);
                    $this->app['router']->middlewareGroup(
                        $middleware,
                        ["Frontend\\$portalDivision\\$module\\Middleware\\$middleware"]
                    );
                }
            }
        }

        // Load configuration files
        $configPath = "$modulePath/config";
        if (is_dir($configPath)) {
            $this->loadConfigFiles($configPath, $portalDivision, $module);
        }

        // Load Livewire components
        $this->loadLivewireComponents($portalDivision, $module, $modulePath);
    }

    protected function loadConfigFiles($configPath, $portalDivision, $module)
    {
        $configFiles = File::glob("$configPath/*.php");
        foreach ($configFiles as $configFile) {
            $configName = pathinfo($configFile, PATHINFO_FILENAME);
            $this->mergeConfigFrom($configFile, "frontend.$portalDivision.$module.$configName");
        }
    }

    protected function loadLivewireComponents($portalDivision, $module, $modulePath)
    {
        $livewirePath = "$modulePath/Livewire";

        if (!is_dir($livewirePath)) {
            return;
        }

        $livewireComponents = scandir($livewirePath);

        foreach ($livewireComponents as $componentFile) {
            if ($componentFile === '.' || $componentFile === '..') {
                continue;
            }

            // Assuming the component class name is the same as the file name
            $componentName = pathinfo($componentFile, PATHINFO_FILENAME);

            // Register the Livewire component with the proper namespace and alias
            $namespace = "Frontend\\$portalDivision\\$module\\Livewire";
            $componentClass = "$namespace\\$componentName";
            $livewireString = Str::snake($portalDivision) . "." . Str::snake($module) . "." . Str::snake($componentName);
            Livewire::component($livewireString, $componentClass);
        }
    }
}
