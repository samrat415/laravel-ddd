<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;
use Livewire\Livewire;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class ModuleServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Base path for adapters
        $path = base_path('src');
        // Load modules
        $modules = glob("$path/*");
        foreach ($modules as $module) {
            $module = basename($module);
            // Base path for modules
            $moduleBasePath = base_path("src/$module");
            // Load routes
            $routesPath = "$moduleBasePath/routes";
            $routeFiles = glob("$routesPath/*.php");
            foreach ($routeFiles as $routeFile) {
                include $routeFile;
            }

            // Load middleware
            $middlewarePath = "$moduleBasePath/Middleware";
            $middlewareFiles = glob("$middlewarePath/*.php");
            foreach ($middlewareFiles as $middlewareFile) {
                $middlewareClass = "Src\\$module\\Middleware\\" . basename($middlewareFile, '.php');
                $this->app['router']->middleware($middlewareClass);
            }

            // Load views
            if (is_dir("$moduleBasePath/Views")) {
                $this->loadViewsFrom("$moduleBasePath/Views", $module);
            }

            // Load middlewares
            if (is_dir("$moduleBasePath/Middleware")) {
                foreach (scandir("$moduleBasePath/Middleware") as $file) {
                    $filePath = "$moduleBasePath/Middleware/$file";
                    if (is_file($filePath)) {
                        $middleware = pathinfo($file, PATHINFO_FILENAME);
                        $this->app['router']->middlewareGroup(
                            $middleware,
                            ["$module\\Middleware\\$middleware"]
                        );
                    }
                }
            }

            // Load configuration files
            $configPath = "$moduleBasePath/config";
            if (is_dir($configPath)) {
                $this->loadConfigFiles($configPath, $module);
            }

            // Load Livewire components
            $this->loadLivewireComponents($module);
            $this->loadModuleFactories($moduleBasePath);
            $this->loadModuleTranslations($moduleBasePath, $module);

        }
    }

    protected function loadConfigFiles($configPath, $module)
    {
        $configFiles = File::glob("$configPath/*.php");
        foreach ($configFiles as $configFile) {
            $configName = pathinfo($configFile, PATHINFO_FILENAME);
            $this->mergeConfigFrom($configFile, "src.$module.$configName");
        }
    }

    protected function loadLivewireComponents($module)
    {
        $livewirePath = base_path("src/$module/Livewire");

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
            $namespace = "Src\\$module\\Livewire";
            $componentClass = "$namespace\\$componentName";
            $livewireString = Str::snake($module)."." . Str::snake($componentName);
            Livewire::component($livewireString, $componentClass);
        }
    }
    protected function loadModuleTranslations(string $moduleBasePath, string $module): void
    {
        $langPath = "$moduleBasePath/lang";

        if (is_dir($langPath)) {
            // This enables __('pages.title') for src/Pages/lang/en/pages.php
            $this->loadTranslationsFrom($langPath,strtolower($module));
        }
    }
    protected function loadModuleFactories(string $moduleBasePath): void
    {
        Factory::guessFactoryNamesUsing(function (string $modelName) use ($moduleBasePath) {
            return str_replace('Models', 'Database\\Factories', $modelName) . 'Factory';
        });

        $factoryPath = "$moduleBasePath/Database/Factories";
        if (is_dir($factoryPath)) {
            $this->loadModuleFactoriesFrom($factoryPath);
        }
    }

    protected function loadModuleFactoriesFrom(string $path): void
    {
        foreach (glob($path . '/*.php') as $file) {
            require_once $file;
        }
    }
}

