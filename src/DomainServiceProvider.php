<?php

namespace App\Providers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class DomainServiceProvider extends ServiceProvider
{
//    /**
//     * Register services.
//     */
//    public function register(): void
//    {
//        //
//    }
//
//    /**
//     * Bootstrap services.
//     */
//    public function boot(): void
//    {
//        // Base path for domains
//        $domainsPath = base_path('domains');
//        // Load routes
//        $routeFiles = glob("$domainsPath/*/routes/*.php");
//        foreach ($routeFiles as $routeFile) {
//            include $routeFile;
//        }
//        // Load modules
//        $modules = glob("$domainsPath/*");
//        foreach ($modules as $module) {
//            $moduleName = basename($module,);
//            $configPath = "$module/config";
//            $configFiles = File::glob("$configPath/*.php");
//            foreach ($configFiles as $configFile) {
//                $configName = pathinfo($configFile, PATHINFO_FILENAME);
//                $this->mergeConfigFrom($configFile, "domains.$moduleName.$configName");
//            }
//        }
//    }
//}
    public function boot(): void
    {
        $domainsPath = base_path('domains');

        // Loop over domains (e.g., Domain1, Domain2)
        $domainFolders = glob("$domainsPath/*", GLOB_ONLYDIR);

        foreach ($domainFolders as $domainPath) {
            $domain = basename($domainPath);

            // Loop over modules inside each domain
            $modulePaths = glob("$domainPath/*", GLOB_ONLYDIR);

            foreach ($modulePaths as $modulePath) {
                $module = basename($modulePath);

                // Load route files
                $routeFiles = glob("$modulePath/routes/*.php");
                foreach ($routeFiles as $routeFile) {
                    include $routeFile;
                }

                // Load config files
                $configFiles = File::glob("$modulePath/config/*.php");
                foreach ($configFiles as $configFile) {
                    $configName = pathinfo($configFile, PATHINFO_FILENAME);
                    $this->mergeConfigFrom($configFile, "domains.$domain.$module.$configName");
                }
            }
        }
    }
}
