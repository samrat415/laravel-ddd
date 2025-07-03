<?php

namespace Samrat415\LaravelDdd\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeDomainModuleCommand extends Command
{
    protected $signature = 'ddd:domain {name? : ModuleName or PortalName/ModuleName (e.g. Users or Admin/Users)}';
    protected $description = 'Create a new DDD module inside domains/<portal>/<module>';

    public function handle(): int
    {
        $input = $this->argument('name');

        if (!$input) {
            $this->error('❌ You must provide a module name or portal/module name.');
            return self::INVALID;
        }

        if (str_contains($input, '/')) {
            // portal/module provided
            [$portal, $module] = explode('/', $input, 2);
            if (!$this->portalExists($portal)) {
                $this->error("❌ Portal '$portal' does not exist in domains/");
                $portal = $this->askForPortal();
            }
        } else {
            // only module provided, ask portal
            $portal = $this->askForPortal();
            $module = $input;
        }

        if (!$portal) {
            $this->error('❌ No portal selected. Aborting.');
            return self::FAILURE;
        }

        $basePath = base_path("domains/$portal/$module");

        if (is_dir($basePath)) {
            $this->error("❌ Module $module already exists in portal $portal.");
            return self::FAILURE;
        }

        $paths = [
            "$basePath/Api",
            "$basePath/Resources",
            "$basePath/routes",
        ];

        foreach ($paths as $path) {
            File::makeDirectory($path, 0755, true);
        }

        $routesV1Path = "$basePath/routes/v1.php";
        if (!file_exists($routesV1Path)) {
            File::put($routesV1Path, "<?php\n\nuse Illuminate\\Support\\Facades\\Route;\n\n// Define v1 API routes here\n");
        }

        $this->info("✅ Domain module created at: domains/$portal/$module");
        return self::SUCCESS;
    }

    protected function portalExists(string $portal): bool
    {
        return is_dir(base_path("domains/$portal"));
    }

    protected function askForPortal(): ?string
    {
        $portals = $this->getPortals();

        if (empty($portals)) {
            $this->warn('⚠️ No portals found in domains/. You must create one first.');
            if ($this->confirm('Do you want to create a new portal now?')) {
                $newPortal = $this->ask('Enter new portal name');
                if ($newPortal) {
                    File::makeDirectory(base_path("domains/$newPortal"), 0755, true);
                    $this->info("📁 Portal created: domains/$newPortal");
                    return $newPortal;
                }
            }
            return null;
        }

        $portal = $this->choice('Select a portal to use', $portals);

        return $portal;
    }

    protected function getPortals(): array
    {
        $domainsPath = base_path('domains');

        if (!is_dir($domainsPath)) {
            return [];
        }

        $folders = glob($domainsPath . '/*', GLOB_ONLYDIR);

        return array_map(fn($f) => basename($f), $folders);
    }
}
