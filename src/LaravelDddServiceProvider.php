<?php

namespace Samrat415\LaravelDdd;

use Samrat415\LaravelDdd\Commands\DddInstallCommand;
use Samrat415\LaravelDdd\Commands\MakeDomainModuleCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Samrat415\LaravelDdd\Commands\LaravelDddCommand;

class LaravelDddServiceProvider extends PackageServiceProvider
{
    public function boot()
    {
        $this->app->register(DomainServiceProvider::class);
        $this->app->register(FrontendServiceProvider::class);
        $this->app->register(ModuleServiceProvider::class);

        if ($this->app->runningInConsole()) {
            $this->commands([
                \Samrat415\LaravelDdd\Commands\LaravelDddCommand::class,
                \Samrat415\LaravelDdd\Commands\DddInstallCommand::class,
                \Samrat415\LaravelDdd\Commands\MakeDomainModuleCommand::class,
                \Samrat415\LaravelDdd\Commands\MakeSrcModuleCommand::class,
            ]);
        }
    }

    public function register()
    {
        $this->addPsr4Path('Domains', base_path('domains'));
        $this->addPsr4Path('Src', base_path('src'));
        $this->addPsr4Path('Frontend', base_path('frontend'));
    }
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-ddd')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel_ddd_table');
    }

    protected function addPsr4Path(string $namespace, string $path)
    {
        if (!class_exists(\Composer\Autoload\ClassLoader::class)) {
            return;
        }

        $loader = require base_path('/vendor/autoload.php');

        if ($loader instanceof \Composer\Autoload\ClassLoader) {
            $loader->addPsr4("$namespace\\", rtrim($path, '/') . '/');
        }
    }
}
